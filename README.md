<h1>Kad Kahwin Digital</h1>
<p>This project is related to make Kad Kahwin Digital</p>

<h2>Table of Contents</h2>
<ol>
  <li>Project Structure</li>
  <li>Prerequisites</li>
  <li>Setup Guide</li>
  <li>Clone the Repository</li>
  <li>Backend Setup</li>
  <li>Frontend Setup</li>
  <li>Additional Notes</li>
</ol>
<hr>
<h3>Project Structure</h3>
<pre>
  <code>
   root-folder/
├── backend/        # Backend services
├── frontend/       # Frontend application
|── .vscode/        # Workspace-specific settings and tasks
|──.gitignore      # will not include if push to repo
  </code>
</pre>
<hr>
<h3>Prerequisites</h3>
<p>Make sure you have the following installed on your machine:</p>

<ul>
  <li>Node.js (>= v20.x): Download</li>
  <li>nvm</li>
  <li>Git: Download</li>
  <li>Any preferred code editor (e.g., VS Code)</li>
</ul>
<hr>
<h3>Setup Guide</h3>
<h4>Clone the repository</h4>
  <p>1. Clone the repository using HTTPS</p>
<pre>
  <code>git clone https://github.com/themuaz97/kad-kahwin-digital.git
</code>
</pre>
<p>2. Navigate into the project directory:</p>
<pre>
  <code>cd kad-kahwin-digital
</code>
</pre>
<h4>Backend setup</h4>
<p>1. Navigate to the <code>backend</code> folder</p>
<pre>
  <code>cd backend</code>
</pre>

<p>2. Install dependencies</p>
<pre>
  <code>npm install</code>
</pre>

<p>3. Configure environment variables</p>
<ul>
  <li>create a <code>.env</code> file in the backend directory (can take from .env.example)</li>
  <li>add the following variables (adjust as needed)</li>
</ul>
<pre>
  <code>
    PORT=3000
    DATABASE_URL=your-database-url
    JWT_SECRET=your-jwt-secret
  </code>
</pre>

<p>4. start backend server</p>
<pre>
  <code>npm run dev</code>
</pre>

<h4>Frontend setup</h4>
<p>1. Navigate to the <code>frontend</code> folder</p>
<pre>
  <code>cd frontend</code>
</pre>

<p>2. Install dependencies</p>
<pre>
  <code>npm install</code>
</pre>

<p>3. Configure environment variables</p>
<ul>
  <li>create a <code>.env</code> file in the frontend directory (can take from .env.example)</li>
  <li>add the following variables (adjust as needed)</li>
</ul>
<pre>
  <code>
    VITE_BASE_URL=http://localhost:3000
  </code>
</pre>

<p>4. start frontend app</p>
<pre>
  <code>npm run dev</code>
</pre>

<h3>Additional Notes</h3>
<p>if using vscode, please use workspace as provided at root folder. All required extensions already setup there.</p>

