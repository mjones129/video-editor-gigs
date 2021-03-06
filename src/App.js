import logo from "./logo.svg";
import "./App.css";
import Example from "./partials/header.js";

function App() {
  return (
    <div className="App">
      <div>
        <Example />
      </div>
      <header className="App-header">
        <h1>Video Editor Gigs</h1>
        <img src={logo} className="App-logo" alt="logo" />
        <p>
          Edit <code>src/App.js</code> and save to reload.
        </p>
        <a
          className="App-link"
          href="https://reactjs.org"
          target="_blank"
          rel="noopener noreferrer"
        >
          Learn React
        </a>
      </header>
    </div>
  );
}

export default App;
