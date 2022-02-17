import { BooleanField, DateField, ListField } from '../../components/Table';
import { MUIDataTableColumn } from 'mui-datatables';
import BasePageList from '../../components/BasePageList';

type Category = {
    name: string;
}
const columns: MUIDataTableColumn[] =[
    {
        name:"name",
        label:"Nome"
    },
    {
        name:"categories",
        label:"Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                const categories: string[] = (value as Array<Category>).map(v => v.name);
                return <ListField values={categories}  />
            }
        }
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
            pageName="Gêneros"
            api="genres"
            columnsDefinition={columns}
        />
    );
};

export default List;