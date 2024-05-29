let baseUrl;

if (process.env.NODE_ENV === 'development') {
    // Environnement de développement
    baseUrl = 'http://localhost:8000';
} else {
    // Environnement de production
    baseUrl = "https://foodme.pydonze.fr";
}

export default baseUrl;