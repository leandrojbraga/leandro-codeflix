import React from 'react';
import { makeStyles, Theme, createStyles } from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import MuiBreadcrumbs from '@material-ui/core/Breadcrumbs';
import { Route } from 'react-router';
import LinkRouter from './LinkRouter';
import Location from 'history';
import routes from '../routes';
import RouteParser from 'route-parser';
import { Container } from '@material-ui/core';

const breadcrumbNameMap: { [key: string]: string } = {}
routes.forEach(
    route => breadcrumbNameMap[route.path as string] = route.label
);

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    root: {
      display: 'flex',
      flexDirection: 'column',
      paddingTop: 20,
      paddingLeft: theme.spacing(4),
    },
    toolbarSpace: theme.mixins.toolbar,
  }),
);


export default function Breadcrumbs() {
  const classes = useStyles();

  function makeBreadcrumb(location: Location) {
    const pathnames = location.pathname.split('/').filter((x) => x);
    pathnames.unshift('/');

    return (
        <MuiBreadcrumbs aria-label="breadcrumb">
            {pathnames.map((value, index) => {
            const last = index === pathnames.length - 1;
            const to = `${pathnames.slice(0, index + 1).join('/').replace('//', '/')}`;
            
            const route = Object.keys(
                breadcrumbNameMap
            ).find(
                path => new RouteParser(path).match(to)
            );
            if (route === undefined) return false;

            return last ? (
                <Typography color="textPrimary" key={to}>
                {breadcrumbNameMap[route]}
                </Typography>
            ) : (
                <LinkRouter color="inherit" to={to} key={to}>
                {breadcrumbNameMap[route]}
                </LinkRouter>
            );
            })}
        </MuiBreadcrumbs>
    )
  }

  return (
        <React.Fragment key='Breadcrumbs'>
            <div className={classes.toolbarSpace} />
            <Container className={classes.root}>
                <Route>
                {
                    ({location}: {location: Location}) => makeBreadcrumb(location)
                }
                </Route>
            </Container>
        </React.Fragment>
  );
}