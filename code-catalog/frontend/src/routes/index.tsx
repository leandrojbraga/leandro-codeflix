
import { RouteProps } from "react-router-dom";
import CastMemberList from "../pages/cast-members/List";
import CategoryList from "../pages/categories/List";
import ContentDescriptorList from "../pages/content-descriptors/List";
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
    {
        name: 'genres.list',
        label: 'Gêneros',
        path: '/genres',
        component: GenreList,
        exact: true
    },
    {
        name: 'cast-members.list',
        label: 'Elenco e equipe',
        path: '/cast-members',
        component: CastMemberList,
        exact: true
    },
    {
        name: 'content-descriptors',
        label: 'Classificação de conteúdo',
        path: '/content-descriptors',
        component: ContentDescriptorList,
        exact: true
    }
];

export default routes;