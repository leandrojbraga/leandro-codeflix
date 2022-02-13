import * as React from 'react';

import CategoryIcon from '@material-ui/icons/Category';
import MovieFilterRoundedIcon from '@material-ui/icons/MovieFilterRounded';
import { List, ListItem, ListItemIcon, ListItemText } from '@material-ui/core';

const menuItens = {
    categories: {
        label: "Categorias",
        icon: <CategoryIcon/>
    },
    genres: {
        label: "Gênero",
        icon: <MovieFilterRoundedIcon/>
    }
}

export const MenuItens = () => {
    return (
        <React.Fragment key='MenuItens'>
             <List>
                {Object.entries(menuItens).map(([key, item]) => (
                    <ListItem button key={key}>
                        <ListItemIcon>
                            {item.icon}
                        </ListItemIcon>
                        <ListItemText primary={item.label} />
                    </ListItem>
                ))}
            </List>
        </React.Fragment>
    );
};