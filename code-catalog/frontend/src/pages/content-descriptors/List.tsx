import { DateField } from '../../components/Table';
import { MUIDataTableColumn } from 'mui-datatables';
import BasePageList from '../../components/BasePageList';

const columns: MUIDataTableColumn[] =[
    {
        name:"name",
        label:"Nome"
    },
    {
        name:"created_at",
        label:"Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <DateField value={value as string}  />
            }
        }
    },
];

const List = () => {
    return (
        <BasePageList
            pageName="Classificação de conteúdo"
            api="content-descriptors"
            columnsDefinition={columns}
        />
    );
};

export default List;