using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Text;
using System.Windows.Forms;
using System.Net;
using System.IO;

namespace WebDKPSync
{
    public partial class MainForm : Form
    {
        /// <summary>
        /// Keeps track of whether the background worker was canceled by the user clicking the cancel button
        /// </summary>
        bool backgroundWorkerCanceled;
        /// <summary>
        /// Keeps track of whether the background worker encountered an error
        /// </summary>
        bool backgroundWorkerHadError;
        /// <summary>
        /// If background worker encounters an error, this holds an appropirate error message
        /// </summary>
        string backgroundWorkerError;
        /// <summary>
        /// Holds true if the upload timed out
        /// </summary>
        bool timedOut = false;
        /// <summary>
        /// Counts how many times the current operation has timed out
        /// </summary>
        int timeOutCount = 0;

        /// <summary>
        /// The url to upload files to
        /// </summary>
        //string siteUrlManage = "http://loki/dkp/client/upload";//;//
        //string siteUrlNormal = "http://loki/dkp/client/download"; //;

        //string siteUrlManage = "http://www.webdkp.com/client/upload";
        //string siteUrlNormal = "http://www.webdkp.com/client/download";

        string siteUrlManage = "/client/upload";
        string siteUrlNormal = "/client/download";

        //string siteUrlManage = "http://ironclaw/webdkp/manageDkp.php";
        //string siteUrlNormal = "http://ironclaw/webdkp/dkp.php";
        /// <summary>
        /// The upload task tells what async task to perform in the background worker
        /// </summary>
        string uploadTask = "";
        public MainForm()
        {
            InitializeComponent();
            backgroundWorkerCanceled = false;
            buttonCancel.Enabled = false;
        }

 
        #region Saving and loading properties
        /// <summary>
        /// Save current settings when the form closes
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void MainForm_FormClosing(object sender, FormClosingEventArgs e)
        {
            Properties.Settings.Default.logfile = inputLogFile.Text;
            Properties.Settings.Default.username = inputUsername.Text;
            Properties.Settings.Default.password = inputPassword.Text;
            Properties.Settings.Default.siteurl = inputSiteURL.Text;
            Properties.Settings.Default.Save();
        }

        /// <summary>
        /// Load current settings on load
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void MainForm_Load(object sender, EventArgs e)
        {
            inputLogFile.Text = Properties.Settings.Default.logfile;
            inputPassword.Text = Properties.Settings.Default.password;
            inputUsername.Text = Properties.Settings.Default.username;
            inputSiteURL.Text = Properties.Settings.Default.siteurl;
        }
        #endregion 

        #region Button Event Handlers

        /// <summary>
        /// Event handler for the browse button next to the log file location. 
        /// Has a file browser appear so the log file can be selected
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void buttonBrowseLog_Click(object sender, EventArgs e)
        {
            fileBrowser.Filter = "Log File|*.lua";
            fileBrowser.ShowDialog();
            if (fileBrowser.FileName != "openFileDialog1")
            {
                inputLogFile.Text = fileBrowser.FileName;
            }
        }

        /// <summary>
        /// Event handler for synchronized button. Causes current log file to be
        /// uploaded to the website and the current dkp table to be downloaded
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void buttonSync_Click(object sender, EventArgs e)
        {
            if (!inputPathOk())
                return;
            labelStatus.Text = "Starting Sync...";
            buttonCancel.Enabled = true;
            buttonSync.Enabled = false;
            buttonGetLatest.Enabled = false;
            buttonErase.Enabled = false; 
            backgroundWorkerHadError = false;
            backgroundWorkerCanceled = false;
            backgroundWorkerError = "";
            uploadTask = "Sync";
            backgroundWorker.RunWorkerAsync();
        }

        /// <summary>
        /// Gets the latest dkp table from the website
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void buttonGetLatest_Click(object sender, EventArgs e)
        {
            if (!inputPathOk())
                return;
            labelStatus.Text = "Starting Download...";
            buttonCancel.Enabled = true;
            buttonSync.Enabled = false;
            buttonGetLatest.Enabled = false;
            buttonErase.Enabled = false; 
            backgroundWorkerHadError = false;
            backgroundWorkerCanceled = false;
            backgroundWorkerError = "";
            uploadTask = "Download";
            backgroundWorker.RunWorkerAsync();
        }

        /// <summary>
        /// Clears your current log file. 
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void buttonErase_Click(object sender, EventArgs e)
        {
            if (!inputPathOk())
                return;
            string logPath = inputLogFile.Text;
            try
            {
                File.Delete(logPath);
                updateStatus("Log file deleted");
            }
            catch
            {
                updateStatus("An error was encoutered while deleting the log file");
            }

        }

        /// <summary>
        /// Event handler for the user clicking the 'cancel' button. 
        /// Causes the upload / download to be canceled. 
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void buttonCancel_Click(object sender, EventArgs e)
        {
            backgroundWorkerCanceled = true;
            backgroundWorker.CancelAsync();
        }

        /// <summary>
        /// Validates that a correct WebDKP.lua file was entered in the directory input
        /// </summary>
        /// <returns>True if it is valid, false otherwise</returns>
        private bool inputPathOk()
        {
            string inputPath = inputLogFile.Text;
            if (inputPath.ToLower().Contains("\\interface\\addons\\"))
            {
                MessageBox.Show("Warning, wrong WebDKP.lua file selected. \nThe WebDKP.lua file you want to upload is located in: \n\nWorld of Warcraft\\WTF\\Account\\Account_Name\\SavedVariables\\WebDKP.lua", "Wrong WebDKP.lua file selected", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                return false;
            }
            return true;
        }

        #endregion

        #region Background Worker code (harness for different tasks in different thread)
        private void backgroundWorker_DoWork(object sender, DoWorkEventArgs e)
        {
            if (uploadTask == "Sync")
            {
                uploadStatusFromThread("Uploading log (this may take some time)");
                timedOut = false;
                timeOutCount = 0; 
                try
                {
                    uploadLogFile();
                    //if upload timed out, give it another go
                    while (timedOut && timeOutCount < 10){
                        if (backgroundWorker.CancellationPending)
                            return;
                        uploadStatusFromThread("Timed out, resuming upload ("+timeOutCount+")");
                        uploadLogFile();
                    }
                    //if we still timed out after all that, somethings wrong
                    if (timedOut){
                        backgroundWorkerHadError = true;
                        backgroundWorkerError = "The upload timed out 10 times - the site must be very busy or down. \n Please wait a bit before trying to upload again.";
                    }

                }
                catch { }
                if (backgroundWorkerHadError)
                    return;
                if (backgroundWorker.CancellationPending)
                    return;
                uploadStatusFromThread("Downloading current table");
                downloadDkpTable();
            }
            else if (uploadTask == "Download")
            {
                downloadDkpTable();
            }
        }

        private void backgroundWorker_ProgressChanged(object sender, ProgressChangedEventArgs e)
        {

        }

        private void backgroundWorker_RunWorkerCompleted(object sender, RunWorkerCompletedEventArgs e)
        {
            labelStatus.Text = "Operation complete";
            if (backgroundWorkerHadError)
                MessageBox.Show(backgroundWorkerError, "Operation Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            else if (backgroundWorkerCanceled)
                MessageBox.Show("Operation Canceled", "Operation Canceled", MessageBoxButtons.OK, MessageBoxIcon.Hand);
           
            //fix the buttons so they can be used again
            buttonCancel.Enabled = false;
            buttonSync.Enabled = true;
            buttonGetLatest.Enabled = true;
            buttonErase.Enabled = true;
 
        }
        #endregion

        /// <summary>
        /// Uploads the specified log file to the website using
        /// the entered username and password
        /// </summary>
        private void uploadLogFile()
        {
            timedOut = false;
            string logPath = this.inputLogFile.Text;
            string username = this.inputUsername.Text;
            string password = this.inputPassword.Text;
            string strsite = this.inputSiteURL.Text;
            string strurl = "http://" + strsite + this.siteUrlManage;

            //if the log file doesn't exist, just skip this step and move onto downloading
            if (!File.Exists(logPath))
                return;

            //create the webclient to upload
            string response="";
            try
            {
                WebClient client = new WebClient();
                client.QueryString.Add("event", "UploadLogFromClient");
                client.QueryString.Add("user", username);
                client.QueryString.Add("password", password);
                client.QueryString.Add("clientApp", "true");
                client.QueryString.Add("siteurl", strsite);
                byte[] responseRaw = client.UploadFile(strurl, "POST", logPath);
                System.Text.ASCIIEncoding encoding = new System.Text.ASCIIEncoding();
                response = encoding.GetString(responseRaw);
            }
            catch (WebException e)
            {
                timedOut = true;
                timeOutCount++;
                return;
            }
            catch (Exception e)
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "An unknown error was encountered while trying to upload data. Site may be down. /n Error: " + e.ToString();
                return;
            }


            
            timeOutCount = 0;
            timedOut = false;

            if (response.Contains("UserErrorPassword"))
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "A bad password was entered for your user name.";
            }
            else if (response.Contains("UserError"))
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "Error logging in as the specified user. Does this user exist?";
            }
            else if (!response.Contains("OK"))
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "Unknown error occured on the site.";
            }
            else if (response.Contains("OK"))
            {
                //if everything went ok, we can go ahead and delete the log file 
                //(since a sync will get the latest data next anyways)
                File.Delete(logPath);
            }
        }

        /// <summary>
        /// Downloads the latest dkp data from the site
        /// </summary>
        private void downloadDkpTable()
        {
            string logPath = this.inputLogFile.Text;
            string username = this.inputUsername.Text;
            string password = this.inputPassword.Text;
            string strsite = this.inputSiteURL.Text;
            string strurl = "http://" + strsite + this.siteUrlNormal;
            
            //create the webclient to upload
            string newTable = "";
            try
            {
                WebClient client = new WebClient();
                client.QueryString.Add("view", "RawData");
                client.QueryString.Add("user", username);
                client.QueryString.Add("password", password);
                client.QueryString.Add("clientApp", "true");
                client.QueryString.Add("siteurl", strsite);
                byte[] responseRaw = client.DownloadData(strurl);
                //newTable = client.DownloadString(siteUrlNormal);
                //byte[] responseRaw = new byte[5];
                System.Text.UTF8Encoding encoding = new System.Text.UTF8Encoding();
                newTable = encoding.GetString(responseRaw);
                int q = 0; 
            }
            catch
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "An error was encountered while trying to get data. Site may be down.";
                return;
            }
            if (newTable.Contains("UserErrorPassword"))
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "A bad password was entered for your user name.";
            }
            else if (newTable.Contains("UserError"))
            {
                backgroundWorkerHadError = true;
                backgroundWorkerError = "Error logging in as the specified user. Does this user exist?";
            }

            //first, we need to read in the current file. (if it doesn't exist, we can just start from scratch)
            string log = "";
            //if we are in a sync process, we should clear the log file and start from scratch (so we don't 
            //keep carrying data around) IF we are just getting latest we need to preserve our current data
            if (uploadTask == "Sync")  //SYNC TASK - DELETE OLD LOG FILE
            {
                try { 
                    //create a backup first
                    //create a name for the backup file
                    string dayString = "WebDKP " + DateTime.Now.ToLocalTime();
                    dayString = dayString.Replace("/", "-");
                    dayString = dayString.Replace(":", ".");
                    //create a directory for it
                    string backupdir = Directory.GetParent( Application.ExecutablePath).ToString()+"\\WebDKP Backups\\";
                    Directory.CreateDirectory(backupdir);
                    string backup = backupdir + dayString + ".lua";
                    //back it up
                    File.Copy(logPath, backup);
                }
                catch (Exception e) {
                    int q = 0;
                }
                //now delete it
                File.Delete(logPath);
            }
            else //DOWNLOAD ONLY TASK - PRESERVE CURRENT DATA
            {
                try
                {
                    StreamReader reader = new StreamReader(logPath);
                    log = reader.ReadToEnd();
                    reader.Close();
                }
                catch { }
                //cut off the old table
                int tableStart = log.IndexOf("WebDKP_DkpTable = {");
                if(tableStart>=0)
                    log = log.Remove(tableStart);
            }

            //now place in our new table :)
            log += newTable;
            //now we need to write all this back to the log
            try
            {
                File.Delete(logPath);
            }
            catch { }
            StreamWriter writer = new StreamWriter(logPath);
            writer.Write(log);
            writer.Flush();
            writer.Close();
        }

        #region Update GUI Status Code
        /// <summary>
        /// Thread safe accessors to update the GUI
        /// </summary>
        /// <param name="newStatus"></param>
        private void uploadStatusFromThread(string newStatus)
        {
            this.BeginInvoke(new UpdateStatusDelegate(updateStatus), new object[] { newStatus });
        }

        delegate void UpdateStatusDelegate(string status); 
        private void updateStatus(string newStatus)
        {
            labelStatus.Text = newStatus;
        }
        #endregion

    }
}