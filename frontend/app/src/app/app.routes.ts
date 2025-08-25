import { Routes } from '@angular/router';
import { Login } from './components/auth/login/login';
import { ProductList } from './components/products/product-list/product-list';
import { ProductForm } from './components/products/product-form/product-form';
import { ProductView } from './components/products/product-view/product-view';

export const routes: Routes = [
  { path: 'login', component: Login },
  { path: 'products', component: ProductList },
  { path: 'products/new', component: ProductForm },
  { path: 'products/edit/:id', component: ProductForm },
  { path: 'products/view/:id', component: ProductView },
  { path: '', redirectTo: '/login', pathMatch: 'full' },
];
