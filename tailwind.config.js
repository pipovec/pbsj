module.exports = {
    content: [
        "./assets/**/*.js",
        "./assets/**/*.vue",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {},
        fontFamily: {
            sans: ['Open Sans', 'sans-serif']
        }
    },
    variants: {
        extend: {
            padding: ['hover'],
            maxHeight: ['focus'],
        },
    },
    plugins: [],
    corePlugins: {},
}
