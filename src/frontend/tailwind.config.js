module.exports = {
  content: [
    './src/**/*.{js,ts,jsx,tsx,mdx}'
  ],
  theme: {
    extend: {
      colors: {
        'dbm-color-black': '#000',
        'dbm-color-white': '#fff',
        'dbm-color-primary': '#061C3D',
        'dbm-color-primary-dark': '#111927',
        'dbm-color-primary-light': '#2F3746',
        'dbm-color-secondary': '#FFAD4C',
        'dbm-color-secondary-dark': '#F79009',
        'dbm-color-secondary-light': '#56719F',
        'dbm-color-1': '#B42318',
        'dbm-color-2': '#79747E',
        'dbm-color-3': '#6C737F',
        'dbm-color-4': '#f1f1f1',
        'dbm-color-5': '#FAFAFA',
        'dbm-color-6': '#DBDBDB',
        'dbm-color-7': '#F9FAFB',
        'dbm-color-8': 'rgba(99, 102, 242, 0.08)',
        'dbm-color-9': '#6C84C0',
        'dbm-color-10': '#F6F6F6',
        'dbm-color-11': '#90A8CA',
        'dbm-color-12': '#DEE7F5',
        'dbm-color-13': '#F0F1F3',
        'dbm-color-14': '#E4E4EC',
        'dbm-color-15': '#1D1B20',
        'dbm-color-16': '#E2E2EA',
        'dbm-color-17': '#E4E4E4',
      },

      fontFamily: {
        jakarta: ['"Plus Jakarta Sans"', 'sans-serif']
      },

      fontSize: {
        xxs: ['10px', '120%'],
        'text-xs': ['12px', '120%'],
        'text-xsplus': ['13px', '14px'],
        'text-sm': ['14px', '120%'],
        'text-base': ['16px', '120%'],
        'text-lg': ['18px', '120%'],
        '3-4xl': ['32px', 'normal'],
        '6-7lg': ['64px', 'normal'],
      },

      boxShadow: {
        'dbm-0': '0 4px 34.8px 0 rgba(0, 0, 0, 0.08);',
        'dbm-1': '0 0 13.4px 0 rgba(0, 0, 0, 0.16);',
        'dbm-2': '0 6px 14px 3px #fff;',
        'dbm-3': '0 0 13.4px 0 rgba(0, 0, 0, 0.16);'
      },

      letterSpacing: {
        'dbm-0': '0.07px'
      },

      borderWidth: {
        3: '3px'
      }
    }
  },
  plugins: []
};
