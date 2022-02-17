import { BooleanField, DateField } from '../../components/Table';
import { MUIDataTableColumn } from 'mui-datatables';
import BasePageList from '../../components/BasePageList';

const columns: MUIDataTableColumn[] =[
    {
        name:"name",
        label:"Nome"
    },
    {
        name:"is_active",
        label:"Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <BooleanField value={value as boolean}  />
            }
        }
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
            pageName="Categoria"
            api="categories"
            columnsDefinition={columns}
        />
    );
};

export default List;