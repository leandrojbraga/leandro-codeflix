
import { RouteProps } from "react-router-dom";
import CategoryList from "../pages/categories/List";
import Dashboard from "../pages/Dashboard";
import GenreList from "../pages/genres/List";


export interface AppRouteProps extends RouteProps {
    name: string;
    label: string;
}

const routes: AppRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: 'categories.list',
        label: 'Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    },
    // {
    //     name: 'categories.create',
    //     label: 'Nova',
    //     path: '/categories/create',
    //     component: CategoryList,
    //     exact: true
    // },
    // {
    //     name: 'categories.edit',
    //     label: 'Edição',
    //     path: '/categories/:id/edit',
    //     component: CategoryList,
    //     exact: true
    // },
    {
        name: 'genres.list',
        label: 'Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true
    }
];

export default routes;