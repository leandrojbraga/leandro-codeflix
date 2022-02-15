import * as React from 'react';

import CategoryIcon from '@material-ui/icons/Category';
import MovieFilterRoundedIcon from '@material-ui/icons/MovieFilterRounded';
import { List, ListItem, ListItemIcon, ListItemText } from '@material-ui/core';
import routes, { AppRouteProps } from '../../routes';
import { Link } from 'react-router-dom';


type ItemInfoMenu = {
    icon: React.ReactElement;
    route?: AppRouteProps;
}
const menuItens: { [key: string]: ItemInfoMenu } = {
    'dashboard': {
        icon: <MovieFilterRoundedIcon/>
    },
    'categories.list': {
        icon: <CategoryIcon/>
    }
};

routes.filter(
    route => Object.keys(menuItens).includes(route.name)
).forEach(
    filterRoute => menuItens[filterRoute.name].route = filterRoute
);

export const MenuItens = () => {
    return (
        <React.Fragment key='MenuItens'>
             <List>
                {
                    Object.keys(menuItens).map(
                        (itemName, key) => {
                            const route = menuItens[itemName].route as AppRouteProps;
                            return (
                                <ListItem button 
                                    key={key} 
                                    component={Link}
                                    to={route.path as string}
                                >
                                    <ListItemIcon>
                                        {menuItens[itemName].icon}
                                    </ListItemIcon>
                                    <ListItemText primary={route.label} />
                                </ListItem>
                            )
                        }
                    )
                }
            </List>
        </React.Fragment>
    );
};