export default [
    {
        path: '/',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: {
            layout: 'full',
            permissions: [],
            breadcrumb: 'Login'
        },
    },
    {
        path: '/home',
        name: 'home',
        component: () => import('../pages/Home/home.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['users-all', 'users-view'],
            breadcrumb: 'Inicio'
        },
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('../pages/Users.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['users-all', 'users-view'],
            breadcrumb: 'Usuarios'
        },

    },

    {
        path: '/roles',
        name: 'roles',
        component: () => import('../pages/Roles.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['roles-all', 'roles-view'],
            breadcrumb: 'Roles'
        },

    },

    {
        path: '/permissions',
        name: 'permissions',
        component: () => import('../pages/Permissions.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['permissions-all', 'permissions-view'],
            breadcrumb: 'Permisos'
        },
    },

    {
        path: '/especialidad',
        name: 'especialidad',
        component: () => import('../pages/Especialidad/Especialidades.vue'),
        meta: {
            layout: 'dashboard',
            permissions: ['specialties-all', 'specialties-view'],
            breadcrumb: 'Especialidades'
        },
    },
    {
        path: '/especialidad/:idEspecialidad',
        name: 'programaFormativo',
        component: () => import('../pages/Especialidad/ProgramaFormativo.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['program-all', 'program-view'],
            breadcrumb: 'Programa Formativo'
        }
    },
    {
        path: '/unidadDidactica/:idEspecialidad/:idPrograma',
        name: 'UnidadDidactica',
        component: () => import('../pages/Especialidad/UnidadDidactica.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['units-all', 'units-view'],
            breadcrumb: 'Unidad Didactica'
        }
    },
    {
        path: '/experienciaFormativa/:idEspecialidad/:idPrograma',
        name: 'ExperienciaFormativa',
        component: () => import('../pages/Especialidad/ExperienciaFormativa.vue'),
        props: true, // Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['units-all', 'units-view'],
            breadcrumb: 'Experiencia Formativa'
        }
    },
    {
        path: '/IndicadorLogro/:idEspecialidad/:idPrograma/:idUnidad',
        name: 'IndicadorLogro',
        component: () => import('../pages/Especialidad/IndicadoresLogro.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['indicators-all', 'indicators-view'],
            breadcrumb: 'Indicador de Logro'
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
            breadcrumb: 'Plan Formativo'
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
            breadcrumb: 'Docentes'
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
            breadcrumb: 'Estudiantes'
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
            breadcrumb: 'Sedes'
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
            breadcrumb: 'Grupos'
        },
    },

    {
        path: '/grupos/:idGroupAll',
        name: 'grupoEst',
        component: () => import('../pages/Grupo/Estudiantes.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
            breadcrumb: 'Grupo Estudiante'
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
            breadcrumb: 'Certificado'
        },
    },
    {
        path: '/certificado/:certifyId',
        name: 'certificadoEst',
        component: () => import('../pages/Certificado/Estudiantes.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['groups-all', 'groups-view'],
            breadcrumb: 'Certificado Estudiante'
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
            breadcrumb: 'Matriculas'
        },
    },
    {
        path: '/notas_student',
        name: 'notaStudent',
        component: () => import('../pages/NotaStudent/Grupo.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['note-student-all', 'notas-student-view'],
            breadcrumb: 'Notas'
        },
    },

    {
        path: '/notas/:idNoteStudent',
        name: 'noteByStudent',
        component: () => import('../pages/NotaStudent/viewNota.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['note-student-all', 'notas-student-view'],
            breadcrumb: 'Historial de Notas'
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
            breadcrumb: 'Notas'
        },
    },
    {
        path: '/notasExperience/:idExperiencie',
        name: 'notasExperience',
        component: () => import('../pages/Notas/NotasExperiencia.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
            breadcrumb: 'Notas de Experiencia'
        },
    },
    {
        path: '/notasUnit/:idUnitNote',
        name: 'notasUnits',
        component: () => import('../pages/Notas/NotasUnidades.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
            breadcrumb: 'Notas de Unidades'
        },
    },
    {
        path: '/notas/unidad/:idgroup/:idUnitNote',
        name: 'notasEstUnidad',
        component: () => import('../pages/Notas/AddNotas.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
            breadcrumb: 'Agregar Notas'
        },
    },
    {
        path: '/notas/unidad/:idgroup/:idExperiencie/:idType',
        name: 'notasEst',/// '/notasExperience/:idExperiencie'
        component: () => import('../pages/Notas/AddNotas.vue'),
        props: true, // units-all Esto pasa los parámetros de ruta como props al componente
        meta: {
            layout: 'dashboard',
            permissions: ['notas-all', 'notas-view'],
            breadcrumb: 'Agregar Notas'
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
            breadcrumb: 'Reportes'
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
            breadcrumb: 'Datos Personales'
        },
    },
    {
        path: '/politicas',
        name: 'politicas',
        component: () => import('../pages/Home/politicas.vue'),
        meta: {
            layout: 'full',
            permissions: [],
        },
    },
    {
        path: '/terminos',
        name: 'terminos',
        component: () => import('../pages/Home/terminos.vue'),
        meta: {
            layout: 'full',
            permissions: [],

        }
    }

];
