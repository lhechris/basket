import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import SelectionView from '../views/SelectionView.vue'
import AuthView from '../views/AuthView.vue'
import EntrainementView from '../views/EntrainementView'
import MatchsView from '../views/MatchsView'
import JoueusesView from '../views/JoueusesView'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/selection',
    name: 'selection',
    component: SelectionView
  },
  {
    path: '/entrainement',
    name: 'entrainement',
    component: EntrainementView
  },
  {
    path: '/matchs',
    name: 'matchs',
    component: MatchsView
  },
  {
    path: '/joueuses',
    name: 'joueuses',
    component: JoueusesView
  },
  {
    path: '/auth',
    name: 'auth',
    component: AuthView
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
