import { Box, Container, makeStyles, Theme, Typography } from '@material-ui/core';
import * as React from 'react';
type BasePageProps = {
    title: string
};

const useStyles = makeStyles((theme: Theme) => ({
    title: {
        color: "#999999",
        marginBottom: theme.spacing(2)
    }
}));

export const BasePage:React.FC<BasePageProps> = (props) => {
    const classes = useStyles();

    return (
        <div>
            <Box paddingTop={2}>
                <Container>
                    <Typography
                        component="h1"
                        variant="h5"
                        className={classes.title}
                    >
                        {props.title}
                    </Typography>
                    {props.children}
                </Container>
            </Box>
        </div>
    );
};