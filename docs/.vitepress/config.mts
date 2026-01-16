import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "MIVO",
  description: "Modern Mikrotik Voucher Management System",
  lang: 'en-US',
  cleanUrls: true,
  lastUpdated: true,
  
  head: [
    ['link', { rel: 'icon', href: '/logo.png' }]
  ],

  themeConfig: {
    logo: '/logo.png',
    siteTitle: 'MIVO',
    
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/installation' },
      { text: 'Docker', link: '/guide/docker' },
      { text: 'GitHub', link: 'https://github.com/dyzulk/mivo' }
    ],

    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'What is MIVO?', link: '/' },
          { text: 'Installation', link: '/guide/installation' }
        ]
      },
      {
        text: 'Deployment',
        items: [
          { text: 'Docker Guide', link: '/guide/docker' },
          { text: 'Manual Installation', link: '/guide/installation#manual-installation' },
          { text: 'PaaS / Cloud', link: '/guide/installation#paas-cloud-railway-render-heroku' }
        ]
      },
      {
        text: 'Support',
        items: [
          { text: 'Contribution', link: 'https://github.com/dyzulk/mivo/blob/main/CONTRIBUTING.md' },
          { text: 'Donate', link: 'https://sociabuzz.com/dyzulkdev/tribe' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/dyzulk/mivo' }
    ],

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026 DyzulkDev'
    },

    search: {
      provider: 'local'
    }
  }
})
