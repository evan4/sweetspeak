import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './components/home/Home';

import ProfileEdit from './components/profile/ProfileEdit';

import Categories from './components/categories/Categories';
import CategoryEdit from './components/categories/CategoryEdit';
import CategoryCreate from './components/categories/CategoryCreate';

import Articles from './components/articles/Articles';
import ArticleShow from './components/articles/ArticleShow';
import ArticleCreate from './components/articles/ArticleCreate';

import ArticleEdit from './components/articles/ArticleEdit';

import Comments from './components/comments/Comments';
import CommentsShow from './components/comments/CommentsShow';

import Complaints from './components/complains/Complaints';

import Users from './components/users/Users';
import UserShow from './components/users/UserShow';
import UsersEdit from './components/users/UsersEdit';

import MessagesAll from './components/messages/MessagesAll';
import Messages from './components/messages/Messages';
import MessagesHistory from './components/messages/MessagesHistory';

import Mailing from './components/mailing/Mailing';
import TemplateCreate from './components/mailing/TemplateCreate';
import TemplateEdit from './components/mailing/TemplateEdit';
import TemplateShow from './components/mailing/TemplateShow';

import Friends from './components/friends/Friends';

import NotFound from './components/404/index';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/profile/edit',
    name: 'ProfileEdit',
    component: ProfileEdit,
  },
  {
    path: '/articles',
    component: Articles,
  },
  {
    path: '/articles/create',
    component: ArticleCreate,
  },
  {
    path: '/articles/edit',
    component: ArticleEdit,
  },
  {
    path: '/articles/:id',
    component: ArticleShow,
  },
 
  {
    path: '/categories',
    component: Categories,
  },
  {
    path: '/categories/create',
    component: CategoryCreate,
  },
  {
    path: '/categories/:id',
    component: CategoryEdit,
  },
  {
    path: '/comments',
    component: Comments,
  },
  {
    path: '/comments/:id',
    component: CommentsShow,
  },
  {
    path: '/complaints',
    component: Complaints,
  },
  {
    path: '/messages_all',
    component: MessagesAll,
  },
  {
    path: '/messages',
    component: Messages,
  },
  {
    path: '/messages/:id',
    component: MessagesHistory,
  },
  {
    path: '/users/',
    component: Users,
  },
  {
    path: '/users/edit',
    component: UsersEdit,
  },
  {
    path: '/users/:id',
    component: UserShow,
  },
  {
    path: '/mailing',
    component: Mailing,
  },
  {
    path: '/mailing/create',
    component: TemplateCreate,
  },
  {
    path: '/mailing/edit',
    component: TemplateEdit
  },
  {
    path: '/mailing/:id',
    component: TemplateShow,
  },
   {
    path: '/friends',
    component: Friends
  },
  {
    path: '*',
    name: 'NotFound',
    component: NotFound,
  },
];

const router = new VueRouter({
  routes,
});

export default router;
