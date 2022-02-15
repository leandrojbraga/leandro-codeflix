import Link, { LinkProps } from '@material-ui/core/Link';
import { Link as RouterLink } from 'react-router-dom';
import { makeStyles, Theme, createStyles } from '@material-ui/core/styles';

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    root: {
        color: "#4db5ab",
        textDecoration: "none",
        "&:focus, &:active": {
            color: "#4db5ab"
        },
        "&:hover": {
            color: "#055a52",
            textDecoration: "none"
        }
    }
  }),
);


interface LinkRouterProps extends LinkProps {
    to: string;
    replace?: boolean;
}

export default function LinkRouter(props: LinkRouterProps) {
    const classes = useStyles();
    
    return <Link 
                {...props}
                component={RouterLink as any}
                className={classes.root} 
            />;
}