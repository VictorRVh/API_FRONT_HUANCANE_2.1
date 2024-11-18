import axios from 'axios';

axios.defaults.baseURL = `/api`;
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
