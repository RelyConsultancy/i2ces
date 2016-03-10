export default {
  dashboard: {
    user: null,

    flag: {
      network: false,
    },

    navigation: [{
      label: 'FAQs',
      path: '/faqs',
    }, {
      label: 'Logout',
      path: '/logout',
    }],
  },

  evaluation: {
    filter: {
      category: null,
      brand: null,
      supplier: null,
    },

    list_empty: 'No records found',
    list: [],

    data_empty: 'No data to display',
    data: null,
    chapter: {},
    chapter_palette: [
      '#3778C1',
      '#E58700',
      '#7D3184',
      '#DE0000',
      '#87B900',
      '#A4A4A4',
    ],
  }
}