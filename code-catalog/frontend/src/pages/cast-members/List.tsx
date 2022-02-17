import { DateField } from '../../components/Table';
import { MUIDataTableColumn } from 'mui-datatables';
import BasePageList from '../../components/BasePageList';

enum CastMemberType {
    Diretor = 1,
    Ator = 2
}

const columns: MUIDataTableColumn[] =[
    {
        name:"name",
        label:"Nome"
    },
    {
        name:"type",
        label:"Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return CastMemberType[value]
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
            pageName="Elenco e equipe"
            api="cast-members"
            columnsDefinition={columns}
        />
    );
};

export default List;