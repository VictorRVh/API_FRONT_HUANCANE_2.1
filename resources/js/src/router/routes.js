export default [
    {
        path: '/',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: {
            layout: 'full',
            permissions: [],
        },
    },
    {
        path: '/home',
        name: 'home',
        component: () => import('../pages/Home/home.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['users-all', 'users-view'],
        },
    },

    {
        path: '/users',
        name: 'users',
        component: () => import('../pages/Users.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['users-all', 'users-view'],
        },
    },

    {
        path: '/roles',
        name: 'roles',
        component: () => import('../pages/Roles.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['roles-all', 'roles-view'],
        },
    },

    {
        path: '/permissions',
        name: 'permissions',
        component: () => import('../pages/Permissions.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['permissions-all', 'permissions-view'],
        },
    },

    {
        path: '/especialidad',
        name: 'especialidad',
        component: () => import('../pages/Especialidad/Especialidades.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['specialties-all', 'specialties-view'],
        },
    },
    {
        path: '/especialidad/:idEspecialidad',
        name: 'programaFormativo',
        component: ()=>import('../pages/Especialidad/ProgramaFormativo.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['program-all', 'program-view'],
        }
    },
    {
        path: '/unidadDidactica/:idPrograma',
        name: 'UnidadDidactica',
        component: ()=>import('../pages/Especialidad/UnidadDidactica.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['units-all', 'units-view'],
        }
    },
    {
        path: '/experienciaFormativa/:idPrograma',
        name: 'ExperienciaFormativa',
        component: ()=>import('../pages/Especialidad/ExperienciaFormativa.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['units-all', 'units-view'],
        }
    },
    {
        path: '/IndicadorLogro/:idUnidad',
        name: 'IndicadorLogro',
        component: ()=>import('../pages/Especialidad/IndicadoresLogro.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['indicators-all', 'indicators-view'],
        }
    },
    {
        path: '/Plan',
        name: 'plan',
        component: () => import('../pages/Especialidad/PlanFormativo.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['plan-all', 'plan-view'],
        },
    },
    {
        path: '/docentes',
        name: 'docentes',
        component: () => import('../pages/Docente/Docente.vue'),
        //props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['teachers-all', 'teachers-view'],
        },
    },
    {
        path: '/estudiantes',
        name: 'estudiantes',
        component: () => import('../pages/Estudiante/Estudiante.vue'),
        // props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['students-all', 'students-view'],
        },
    },
    {
        path: '/sede',
        name: 'sedes',
        component: () => import('../pages/Sede/Sede.vue'),
       // props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['places-all', 'places-view'],
        },
    },

    {
        path: '/grupos',
        name: 'grupos',
        component: () => import('../pages/Grupo/Grupo.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
        },
    },

    {
        path: '/grupos/:id',
        name: 'grupoEst',
        component: () => import('../pages/Grupo/Estudiantes.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
        },
    },
    {
        path: '/certificado',
        name: 'certificado',
        component: () => import('../pages/Certificado/Grupo.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
        },
    },
     {
        path: '/certificado/:id',
        name: 'certificadoEst',
        component: () => import('../pages/Certificado/Estudiantes.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
        },
    },
    {
        path: '/matricula',
        name: 'matriculas',
        component: () => import('../pages/Matricula/Matricula.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['enrollmentStudent-all', 'enrollmentStudent-view'],
        },
    },
    {
        path: '/notas',
        name: 'notas',
        component: () => import('../pages/Notas/Grupo.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
        },
    },
    {
        path: '/notasExperience/:id',
        name: 'notasExperience',
        component: () => import('../pages/Notas/NotasExperiencia.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
        },
    },
    {
        path: '/notasUnit/:id',
        name: 'notasUnits',
        component: () => import('../pages/Notas/NotasUnidades.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
        },
    },
    {
        path: '/notas/unidad/:idgroup/:idunit',
        name: 'notasEstUnidad',
        component: () => import('../pages/Notas/AddNotas.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
        },
    },
    {
        path: '/notas/unidad/:idgroup/:idexp/:id',
        name: 'notasEst',
        component: () => import('../pages/Notas/AddNotas.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
        },
    },
    {
        path: '/reporte',
        name: 'reporte',
        component: () => import('../pages/Reporte/Grafico.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['report-all', 'report-view'],
        },
    },
    {
        path: '/datopersonal',
        name: 'datopersonal',
        component: () => import('../pages/Datos/DatoPersonal.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['data-all', 'data-view'],
        },
    }

    /* todlos que se aumento */
];
