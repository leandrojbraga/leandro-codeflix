import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import './App.css';
import Breadcrumbs from './components/Breadcrumbs';
import { Navbar } from './components/Navbar';
import AppRoute from './routes/AppRoute';

function App() {
  return (
    <React.Fragment key='App'>
      <BrowserRouter>
        <Navbar/>
        <Breadcrumbs/>
        <AppRoute/>
      </BrowserRouter>
    </React.Fragment>
  );
}

export default App;
