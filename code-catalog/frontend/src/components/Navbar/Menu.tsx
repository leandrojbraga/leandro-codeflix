// @flow 
import { Box, IconButton, makeStyles, SwipeableDrawer, Theme } from '@material-ui/core';
import MenuIcon from '@material-ui/icons/Menu';
import ClearIcon from '@material-ui/icons/Clear';
import * as React from 'react';
import { MenuItens } from './MenuItens';

const useStyles = makeStyles((theme: Theme) => ({
    iconMenu: {
        '&:hover': {
            backgroundColor: "#222222!important"
         },
    },
    drawerTitle: {
        flexGrow: 1,
        textAlign: "center"
    },
}));

export const Menu = () => {
    const classes = useStyles();

    const [anchorEl, setAnchorEl] = React.useState(false);
    const openStatus = Boolean(anchorEl);

    const handleMenuOpen = () => setAnchorEl(true);
    const handleManuClose = () => setAnchorEl(false);

    return (
        <React.Fragment key='Menu'>
            <IconButton
                edge="start"
                color="inherit"
                aria-label="open drawer"
                aria-controls="menu-appbar"
                aria-haspopup="true"
                className={classes.iconMenu}
                onClick={handleMenuOpen}
            >
                <MenuIcon fontSize="large"/>
            </IconButton>

            <SwipeableDrawer
                id="menu-appbar"
                anchor='left'
                open={openStatus}
                onClose={handleManuClose}
                onOpen={handleMenuOpen}
            >
                <Box p={1}>
                    <IconButton
                        edge="start"
                        color="inherit"
                        aria-label="close drawer"
                        aria-controls="menu-appbar"
                        aria-haspopup="true"
                        onClick={handleManuClose}
                    >
                        <ClearIcon fontSize="small"/>
                    </IconButton>
                </Box>
                <Box onClick={handleManuClose}>
                    <MenuItens/>
                </Box>
            </SwipeableDrawer>
        </React.Fragment>
    );
};