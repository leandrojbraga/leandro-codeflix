import axios from "axios";

export const httpCatalog = axios.create({
    baseURL: process.env.REACT_APP_CATALOG_API_URL
});