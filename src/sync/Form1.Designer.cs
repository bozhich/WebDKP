namespace WebDKPSync
{
    partial class MainForm
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(MainForm));
            this.tabControl = new System.Windows.Forms.TabControl();
            this.tabSync = new System.Windows.Forms.TabPage();
            this.label9 = new System.Windows.Forms.Label();
            this.inputSiteURL = new System.Windows.Forms.TextBox();
            this.label6 = new System.Windows.Forms.Label();
            this.buttonCancel = new System.Windows.Forms.Button();
            this.labelStatus = new System.Windows.Forms.Label();
            this.label5 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.label1 = new System.Windows.Forms.Label();
            this.labelHelp1 = new System.Windows.Forms.Label();
            this.buttonGetLatest = new System.Windows.Forms.Button();
            this.buttonErase = new System.Windows.Forms.Button();
            this.buttonSync = new System.Windows.Forms.Button();
            this.buttonBrowseLog = new System.Windows.Forms.Button();
            this.inputLogFile = new System.Windows.Forms.TextBox();
            this.labelLogFile = new System.Windows.Forms.Label();
            this.inputPassword = new System.Windows.Forms.TextBox();
            this.inputUsername = new System.Windows.Forms.TextBox();
            this.labelPassword = new System.Windows.Forms.Label();
            this.labelUserName = new System.Windows.Forms.Label();
            this.tabInst = new System.Windows.Forms.TabPage();
            this.label4 = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.tabPage1 = new System.Windows.Forms.TabPage();
            this.label7 = new System.Windows.Forms.Label();
            this.fileBrowser = new System.Windows.Forms.OpenFileDialog();
            this.backgroundWorker = new System.ComponentModel.BackgroundWorker();
            this.tabControl.SuspendLayout();
            this.tabSync.SuspendLayout();
            this.tabInst.SuspendLayout();
            this.tabPage1.SuspendLayout();
            this.SuspendLayout();
            // 
            // tabControl
            // 
            this.tabControl.Controls.Add(this.tabSync);
            this.tabControl.Controls.Add(this.tabInst);
            this.tabControl.Controls.Add(this.tabPage1);
            this.tabControl.Location = new System.Drawing.Point(12, 12);
            this.tabControl.Name = "tabControl";
            this.tabControl.SelectedIndex = 0;
            this.tabControl.Size = new System.Drawing.Size(442, 274);
            this.tabControl.TabIndex = 0;
            // 
            // tabSync
            // 
            this.tabSync.Controls.Add(this.label9);
            this.tabSync.Controls.Add(this.inputSiteURL);
            this.tabSync.Controls.Add(this.label6);
            this.tabSync.Controls.Add(this.buttonCancel);
            this.tabSync.Controls.Add(this.labelStatus);
            this.tabSync.Controls.Add(this.label5);
            this.tabSync.Controls.Add(this.label2);
            this.tabSync.Controls.Add(this.label1);
            this.tabSync.Controls.Add(this.labelHelp1);
            this.tabSync.Controls.Add(this.buttonGetLatest);
            this.tabSync.Controls.Add(this.buttonErase);
            this.tabSync.Controls.Add(this.buttonSync);
            this.tabSync.Controls.Add(this.buttonBrowseLog);
            this.tabSync.Controls.Add(this.inputLogFile);
            this.tabSync.Controls.Add(this.labelLogFile);
            this.tabSync.Controls.Add(this.inputPassword);
            this.tabSync.Controls.Add(this.inputUsername);
            this.tabSync.Controls.Add(this.labelPassword);
            this.tabSync.Controls.Add(this.labelUserName);
            this.tabSync.Location = new System.Drawing.Point(4, 22);
            this.tabSync.Name = "tabSync";
            this.tabSync.Padding = new System.Windows.Forms.Padding(3);
            this.tabSync.Size = new System.Drawing.Size(434, 248);
            this.tabSync.TabIndex = 0;
            this.tabSync.Text = "Sync Data";
            this.tabSync.UseVisualStyleBackColor = true;
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Location = new System.Drawing.Point(335, 93);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(12, 13);
            this.label9.TabIndex = 19;
            this.label9.Text = "/";
            // 
            // inputSiteURL
            // 
            this.inputSiteURL.Location = new System.Drawing.Point(100, 89);
            this.inputSiteURL.Name = "inputSiteURL";
            this.inputSiteURL.Size = new System.Drawing.Size(233, 20);
            this.inputSiteURL.TabIndex = 18;
            this.inputSiteURL.Tag = "";
            this.inputSiteURL.Text = "www.webdkp.com";
            // 
            // label6
            // 
            this.label6.AutoSize = true;
            this.label6.Location = new System.Drawing.Point(5, 93);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(90, 13);
            this.label6.TabIndex = 17;
            this.label6.Text = "Site URL:  http://";
            // 
            // buttonCancel
            // 
            this.buttonCancel.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.buttonCancel.Location = new System.Drawing.Point(348, 215);
            this.buttonCancel.Name = "buttonCancel";
            this.buttonCancel.Size = new System.Drawing.Size(80, 21);
            this.buttonCancel.TabIndex = 15;
            this.buttonCancel.Text = "Cancel";
            this.buttonCancel.UseVisualStyleBackColor = true;
            this.buttonCancel.Click += new System.EventHandler(this.buttonCancel_Click);
            // 
            // labelStatus
            // 
            this.labelStatus.AutoSize = true;
            this.labelStatus.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.labelStatus.Location = new System.Drawing.Point(67, 217);
            this.labelStatus.Name = "labelStatus";
            this.labelStatus.Size = new System.Drawing.Size(156, 16);
            this.labelStatus.TabIndex = 14;
            this.labelStatus.Text = "No operation in progress";
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(6, 217);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(55, 16);
            this.label5.TabIndex = 13;
            this.label5.Text = "Status:";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(145, 192);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(97, 13);
            this.label2.TabIndex = 12;
            this.label2.Text = "Erases local log file";
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(145, 163);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(221, 13);
            this.label1.TabIndex = 11;
            this.label1.Text = "Downloads latest table (does not upload logs)";
            // 
            // labelHelp1
            // 
            this.labelHelp1.AutoSize = true;
            this.labelHelp1.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.labelHelp1.Location = new System.Drawing.Point(145, 129);
            this.labelHelp1.Name = "labelHelp1";
            this.labelHelp1.Size = new System.Drawing.Size(274, 16);
            this.labelHelp1.TabIndex = 10;
            this.labelHelp1.Text = "Uploads log and downloads latest dkp";
            // 
            // buttonGetLatest
            // 
            this.buttonGetLatest.Location = new System.Drawing.Point(9, 158);
            this.buttonGetLatest.Name = "buttonGetLatest";
            this.buttonGetLatest.Size = new System.Drawing.Size(130, 23);
            this.buttonGetLatest.TabIndex = 9;
            this.buttonGetLatest.Text = "Get Latest DKP Table";
            this.buttonGetLatest.UseVisualStyleBackColor = true;
            this.buttonGetLatest.Click += new System.EventHandler(this.buttonGetLatest_Click);
            // 
            // buttonErase
            // 
            this.buttonErase.Location = new System.Drawing.Point(9, 187);
            this.buttonErase.Name = "buttonErase";
            this.buttonErase.Size = new System.Drawing.Size(130, 23);
            this.buttonErase.TabIndex = 8;
            this.buttonErase.Text = "Erase Local Log File";
            this.buttonErase.UseVisualStyleBackColor = true;
            this.buttonErase.Click += new System.EventHandler(this.buttonErase_Click);
            // 
            // buttonSync
            // 
            this.buttonSync.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.buttonSync.Location = new System.Drawing.Point(9, 117);
            this.buttonSync.Name = "buttonSync";
            this.buttonSync.Size = new System.Drawing.Size(130, 35);
            this.buttonSync.TabIndex = 7;
            this.buttonSync.Text = "Synchronize!";
            this.buttonSync.UseVisualStyleBackColor = true;
            this.buttonSync.Click += new System.EventHandler(this.buttonSync_Click);
            // 
            // buttonBrowseLog
            // 
            this.buttonBrowseLog.Location = new System.Drawing.Point(339, 61);
            this.buttonBrowseLog.Name = "buttonBrowseLog";
            this.buttonBrowseLog.Size = new System.Drawing.Size(80, 23);
            this.buttonBrowseLog.TabIndex = 6;
            this.buttonBrowseLog.Text = "Browse";
            this.buttonBrowseLog.UseVisualStyleBackColor = true;
            this.buttonBrowseLog.Click += new System.EventHandler(this.buttonBrowseLog_Click);
            // 
            // inputLogFile
            // 
            this.inputLogFile.Location = new System.Drawing.Point(100, 63);
            this.inputLogFile.Name = "inputLogFile";
            this.inputLogFile.Size = new System.Drawing.Size(233, 20);
            this.inputLogFile.TabIndex = 5;
            // 
            // labelLogFile
            // 
            this.labelLogFile.AutoSize = true;
            this.labelLogFile.Location = new System.Drawing.Point(6, 66);
            this.labelLogFile.Name = "labelLogFile";
            this.labelLogFile.Size = new System.Drawing.Size(47, 13);
            this.labelLogFile.TabIndex = 4;
            this.labelLogFile.Text = "Log File:";
            // 
            // inputPassword
            // 
            this.inputPassword.Location = new System.Drawing.Point(100, 37);
            this.inputPassword.Name = "inputPassword";
            this.inputPassword.PasswordChar = '*';
            this.inputPassword.Size = new System.Drawing.Size(175, 20);
            this.inputPassword.TabIndex = 3;
            // 
            // inputUsername
            // 
            this.inputUsername.Location = new System.Drawing.Point(100, 11);
            this.inputUsername.Name = "inputUsername";
            this.inputUsername.Size = new System.Drawing.Size(175, 20);
            this.inputUsername.TabIndex = 2;
            // 
            // labelPassword
            // 
            this.labelPassword.AutoSize = true;
            this.labelPassword.Location = new System.Drawing.Point(6, 40);
            this.labelPassword.Name = "labelPassword";
            this.labelPassword.Size = new System.Drawing.Size(56, 13);
            this.labelPassword.TabIndex = 1;
            this.labelPassword.Text = "Password:";
            // 
            // labelUserName
            // 
            this.labelUserName.AutoSize = true;
            this.labelUserName.Location = new System.Drawing.Point(6, 14);
            this.labelUserName.Name = "labelUserName";
            this.labelUserName.Size = new System.Drawing.Size(58, 13);
            this.labelUserName.TabIndex = 0;
            this.labelUserName.Text = "Username:";
            // 
            // tabInst
            // 
            this.tabInst.Controls.Add(this.label4);
            this.tabInst.Controls.Add(this.label3);
            this.tabInst.Location = new System.Drawing.Point(4, 22);
            this.tabInst.Name = "tabInst";
            this.tabInst.Padding = new System.Windows.Forms.Padding(3);
            this.tabInst.Size = new System.Drawing.Size(434, 248);
            this.tabInst.TabIndex = 1;
            this.tabInst.Text = "Instructions";
            this.tabInst.UseVisualStyleBackColor = true;
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.ForeColor = System.Drawing.Color.DarkRed;
            this.label4.Location = new System.Drawing.Point(6, 163);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(368, 52);
            this.label4.TabIndex = 1;
            this.label4.Text = resources.GetString("label4.Text");
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(6, 15);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(423, 130);
            this.label3.TabIndex = 0;
            this.label3.Text = resources.GetString("label3.Text");
            // 
            // tabPage1
            // 
            this.tabPage1.Controls.Add(this.label7);
            this.tabPage1.Location = new System.Drawing.Point(4, 22);
            this.tabPage1.Name = "tabPage1";
            this.tabPage1.Padding = new System.Windows.Forms.Padding(3);
            this.tabPage1.Size = new System.Drawing.Size(434, 248);
            this.tabPage1.TabIndex = 2;
            this.tabPage1.Text = "Thanks To";
            this.tabPage1.UseVisualStyleBackColor = true;
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Location = new System.Drawing.Point(6, 15);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(406, 208);
            this.label7.TabIndex = 1;
            this.label7.Text = resources.GetString("label7.Text");
            // 
            // fileBrowser
            // 
            this.fileBrowser.FileName = "WebDKP.lua";
            // 
            // backgroundWorker
            // 
            this.backgroundWorker.WorkerSupportsCancellation = true;
            this.backgroundWorker.DoWork += new System.ComponentModel.DoWorkEventHandler(this.backgroundWorker_DoWork);
            this.backgroundWorker.ProgressChanged += new System.ComponentModel.ProgressChangedEventHandler(this.backgroundWorker_ProgressChanged);
            this.backgroundWorker.RunWorkerCompleted += new System.ComponentModel.RunWorkerCompletedEventHandler(this.backgroundWorker_RunWorkerCompleted);
            // 
            // MainForm
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(464, 297);
            this.Controls.Add(this.tabControl);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "MainForm";
            this.Text = "WebDKP Sync Tool";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.MainForm_FormClosing);
            this.Load += new System.EventHandler(this.MainForm_Load);
            this.tabControl.ResumeLayout(false);
            this.tabSync.ResumeLayout(false);
            this.tabSync.PerformLayout();
            this.tabInst.ResumeLayout(false);
            this.tabInst.PerformLayout();
            this.tabPage1.ResumeLayout(false);
            this.tabPage1.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.TabControl tabControl;
        private System.Windows.Forms.TabPage tabSync;
        private System.Windows.Forms.TabPage tabInst;
        private System.Windows.Forms.Button buttonErase;
        private System.Windows.Forms.Button buttonSync;
        private System.Windows.Forms.Button buttonBrowseLog;
        private System.Windows.Forms.TextBox inputLogFile;
        private System.Windows.Forms.Label labelLogFile;
        private System.Windows.Forms.TextBox inputPassword;
        private System.Windows.Forms.TextBox inputUsername;
        private System.Windows.Forms.Label labelPassword;
        private System.Windows.Forms.Label labelUserName;
        private System.Windows.Forms.Button buttonGetLatest;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label labelHelp1;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.OpenFileDialog fileBrowser;
        private System.ComponentModel.BackgroundWorker backgroundWorker;
        private System.Windows.Forms.Label labelStatus;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.Button buttonCancel;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.TextBox inputSiteURL;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.TabPage tabPage1;
        private System.Windows.Forms.Label label7;
    }
}

