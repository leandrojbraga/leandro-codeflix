import { Box, Container, makeStyles, Theme, Typography } from '@material-ui/core';
import * as React from 'react';
type BasePageProps = {
    title: string
};

const useStyles = makeStyles((theme: Theme) => ({
    title: {
        color: "#999999"
    }
}));

export const BasePage:React.FC<BasePageProps> = (props) => {
    const classes = useStyles();

    return (
        <div>
            <Box paddingTop={'20px'}>
                <Container>
                    <Typography
                        component="h1"
                        variant="h4"
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