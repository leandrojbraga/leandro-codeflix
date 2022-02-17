// @flow 
import * as React from 'react';
import { Box, Chip } from '@material-ui/core';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import dateFormat from 'date-fns/format';
import parseISO from 'date-fns/parseISO';
import { DATE_FORMAT_BASIC } from '../util/Definitions';
import { makeStyles, Theme } from '@material-ui/core/styles';

const useStyles = makeStyles((theme: Theme) => ({
    listField: {
        margin: theme.spacing(0.5)
    }
  }),
);

type TableProps = {
    title?: string;
    columnsDefinition: MUIDataTableColumn[];
    data: Array<any>
};
export const Table = (props: TableProps) => {
    return (
        <MUIDataTable
            title={props.title || ""}
            columns={props.columnsDefinition}
            data={props.data}
        />
    );
};

type BooleanFieldProps = {
    value: boolean
};
export const BooleanField = (props: BooleanFieldProps) => {
    return (
        <Chip
            label={props.value ? "Sim" : "Não"}
            color={props.value ? "primary" : "secondary"}
            size="small"
        />
    );
};

type DateFieldProps = {
    value: string,
    format?: string
};
export const DateField = (props: DateFieldProps) => {
    const format = props.format || DATE_FORMAT_BASIC

    return (
        <span>
            {
                dateFormat(
                    parseISO(props.value), format
                )
            }
        </span>
    );
};


type ListFieldProps = {
    values: Array<string>;
};
export const ListField = (props: ListFieldProps) => {
    const classes = useStyles();
    return (
        <Box>
        {
            props.values.map(
                (value) => {
                return (<Chip
                            label={value}
                            color="primary"
                            variant="outlined"
                            size="small"
                            className={classes.listField}
                        />)
                }
            )
        }
    </Box>
    );
        
};