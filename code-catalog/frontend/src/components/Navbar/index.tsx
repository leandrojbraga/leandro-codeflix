import { AppBar, Button, makeStyles, Theme, Toolbar, Typography } from '@material-ui/core';
import * as React from 'react';
import logo from '../../static/image/logo.png';
import { Menu } from './Menu';

const useStyles = makeStyles((theme: Theme) => ({
    toolbar: {
        backgroundColor: '#000000'
    },
    title :{
        flexGrow: 1,
        textAlign: "center"
    },
    button: {
        '&:hover': {
            backgroundColor: "#222222!important"
         },
    },
    logo: {
        width: 100,
        [theme.breakpoints.up("sm")]: {
            width: 140
        },
        [theme.breakpoints.up("md")]: {
            width: 170
        }
    },
}));

export const Navbar: React.FC = () => {
    const classes = useStyles();
    return (
        <AppBar>
            <Toolbar className={classes.toolbar}>
                <Menu/>
                <Typography className={classes.title}>
                    <img src={logo} alt="CodeFlix" className={classes.logo}/>
                </Typography>
                <Button color="inherit" className={classes.button}>Login</Button>
            </Toolbar>
        </AppBar>
    );
};