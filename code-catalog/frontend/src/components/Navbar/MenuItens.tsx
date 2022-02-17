import * as React from 'react';

import MovieFilterRoundedIcon from '@material-ui/icons/MovieFilterRounded';
import CategoryIcon from '@material-ui/icons/Category';
import TheatersIcon from '@material-ui/icons/Theaters';
import PeopleAltIcon from '@material-ui/icons/PeopleAlt';
import ExplicitIcon from '@material-ui/icons/Explicit';
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
    },
    'genres.list': {
        icon: <TheatersIcon/>
    },
    'cast-members.list': {
        icon: <PeopleAltIcon/>
    },
    'content-descriptors': {
        icon: <ExplicitIcon/>
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