import React from 'react';
import './App.css';
import { BasePage } from './components/BasePage';
import { Navbar } from './components/Navbar';

function App() {
  return (
    <React.Fragment key='App'>
      <Navbar/>
      <BasePage title='Principal'>
          Essa é a página principal!!!  
      </BasePage>
    </React.Fragment>
  );
}

export default App;
