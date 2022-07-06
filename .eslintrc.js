module.exports = {
    'env': {
        'browser': true,
        'es2021': true
    },
    'extends': 'eslint:recommended',
    'parserOptions': {
        'ecmaVersion': 'latest'
    },
    'rules': {
        'eqeqeq': 'off',
        'curly': 'error',
        'semi': ['error', 'always'],
        'quotes': ['error', 'single']
    }
};
