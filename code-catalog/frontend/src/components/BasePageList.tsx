import { Box, Fab } from '@material-ui/core';
import {useState, useEffect} from 'react';
import { Link } from 'react-router-dom';
import { BasePage } from './BasePage';
import AddIcon from '@material-ui/icons/Add';
import { MUIDataTableColumn } from 'mui-datatables';
import {httpCatalog} from "../util/http"
import { Table } from './Table';

type BasePageListProps = {
    pageName: string;
    api: string;
    columnsDefinition: MUIDataTableColumn[];
};
const BasePageList = (props: BasePageListProps) => {
    const [data, setData] = useState([]);

    useEffect(
        () => {
            httpCatalog.get(
                props.api
            ).then(
                response => setData(response.data.data)
            )
        },
        [props.api]
    );

    return (
        <BasePage title={`Listagem de ${props.pageName.toLowerCase()}`}>
            <Box dir={'rtl'}>
                <Fab
                    title={`Adicionar${props.pageName.toLowerCase()}`}
                    size="small"
                    component={Link}
                    to={`/${props.api}/create`}
                    disabled={true} // pending develop create page
                >
                    <AddIcon/>
                </Fab>
            </Box>
            <Box marginTop={1} >
                <Table
                    columnsDefinition={props.columnsDefinition}
                    data={data}
                />
            </Box>
        </BasePage>
    );
};

export default BasePageList;