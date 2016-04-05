export default {
  dashboard: {
    user: null,

    flag: {
      network: false,
    },
  },

  evaluation: {
    filter: {
      category: null,
      brand: null,
      supplier: null,
    },

    list_empty: 'No records found',
    list: [],

    evaluation_empty: 'No data to display',
    evaluation: null,

    chapters_cache: {},
    chapter_section: null,
    chapter_empty: 'Loading evaluation data',
    chapter_palette: [
      '#3778C1',
      '#E58700',
      '#7D3184',
      '#DE0000',
      '#87B900',
      '#A4A4A4',
    ],

    stages: {
      pre: 'Pre-Period',
      during: 'Campaign-Period',
      post: 'Post-Period',
    },

    channelIcons: {
      'aisle_fin': 'icon-name',
      'entrance_gate': 'icon-name',
      'sampling': 'icon-name',
      'magazine': 'icon-name',
      'milk_media': 'icon-name',
      '6_sheet': 'icon-name',
      'barkers': 'icon-name',
      'trolleys': 'icon-name',
      'tv_wall': 'icon-name',
    },
  }
}