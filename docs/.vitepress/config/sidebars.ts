import { DefaultTheme } from 'vitepress'

// English Sidebars
export const sidebarEn: DefaultTheme.Sidebar = {
  // Sidebar for /guide/ path
  '/guide/': [
    {
      text: 'Getting Started',
      collapsed: false,
      items: [
        { text: 'Introduction', link: '/guide/' },
        { text: 'Requirements', link: '/guide/installation#requirements' }
      ]
    },
    {
      text: 'Installation',
      collapsed: false,
      items: [
        { text: 'Docker', link: '/guide/docker' },
        { text: 'aaPanel (Docker)', link: '/guide/docker-aapanel' },
        { text: 'Web Server', link: '/guide/installation#web-servers' },
        { text: 'Shared Hosting', link: '/guide/installation#shared-hosting' },
        { text: 'VPS & Cloud', link: '/guide/installation#vps-cloud' },
        { text: 'Mobile & STB', link: '/guide/installation#mobile-stb' }
      ]
    },
    {
      text: 'Configuration',
      items: [
        { text: 'Post-Installation', link: '/guide/installation#post-installation' }
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

  // Sidebar for /manual/ path
  '/manual/': [
    {
      text: 'User Manual',
      items: [
        { text: 'Overview', link: '/manual/' }
      ]
    },
    {
      text: 'Global Settings',
      items: [
        { text: 'Introduction', link: '/manual/settings/' },
        { text: 'Routers', link: '/manual/settings/routers' },
        { text: 'Templates', link: '/manual/settings/templates' },
        { text: 'Logos', link: '/manual/settings/logos' },
        { text: 'API & CORS', link: '/manual/settings/api-cors' },
        { text: 'System', link: '/manual/settings/system' }
      ]
    },
    {
      text: 'Router Operations',
      items: [
        { text: 'Introduction', link: '/manual/router/' },
        { text: 'Dashboard', link: '/manual/router/dashboard' },
        { text: 'Quick Print', link: '/manual/router/quick-print' },
        { text: 'Hotspot Management', link: '/manual/router/hotspot' },
        { text: 'Reports & Logs', link: '/manual/router/reports' },
        { text: 'Network & System', link: '/manual/router/tools' }
      ]
    }
  ]
}

// Indonesian Sidebars
export const sidebarId: DefaultTheme.Sidebar = {
  // Sidebar for /id/guide/ path
  '/id/guide/': [
    {
      text: 'Pengenalan',
      collapsed: false,
      items: [
        { text: 'Apa itu MIVO?', link: '/id/guide/' },
        { text: 'Persyaratan', link: '/id/guide/installation#persyaratan' }
      ]
    },
    {
      text: 'Instalasi',
      collapsed: false,
      items: [
        { text: 'Docker', link: '/id/guide/docker' },
        { text: 'aaPanel (Docker)', link: '/id/guide/docker-aapanel' },
        { text: 'Web Server', link: '/id/guide/installation#web-server' },
        { text: 'Shared Hosting', link: '/id/guide/installation#shared-hosting' },
        { text: 'VPS & Cloud', link: '/id/guide/installation#vps-cloud' },
        { text: 'Mobile & STB', link: '/id/guide/installation#mobile-stb' }
      ]
    },
    {
      text: 'Konfigurasi',
      items: [
        { text: 'Pasca-Instalasi', link: '/id/guide/installation#pasca-instalasi' }
      ]
    },
    {
      text: 'Dukungan',
      items: [
        { text: 'Kontribusi', link: 'https://github.com/dyzulk/mivo/blob/main/CONTRIBUTING.md' },
        { text: 'Donasi', link: 'https://sociabuzz.com/dyzulkdev/tribe' }
      ]
    }
  ],


  // Sidebar for /id/manual/ path
  '/id/manual/': [
    {
      text: 'Buku Manual',
      items: [
        { text: 'Ringkasan', link: '/id/manual/' }
      ]
    },
    {
      text: 'Pengaturan Global',
      items: [
        { text: 'Pendahuluan', link: '/id/manual/settings/' },
        { text: 'Router', link: '/id/manual/settings/routers' },
        { text: 'Template', link: '/id/manual/settings/templates' },
        { text: 'Logo', link: '/id/manual/settings/logos' },
        { text: 'API & CORS', link: '/id/manual/settings/api-cors' },
        { text: 'Sistem', link: '/id/manual/settings/system' }
      ]
    },
    {
      text: 'Operasional Router',
      items: [
        { text: 'Pendahuluan', link: '/id/manual/router/' },
        { text: 'Dashboard', link: '/id/manual/router/dashboard' },
        { text: 'Cetak Cepat', link: '/id/manual/router/quick-print' },
        { text: 'Manajemen Hotspot', link: '/id/manual/router/hotspot' },
        { text: 'Laporan & Log', link: '/id/manual/router/reports' },
        { text: 'Jaringan & Sistem', link: '/id/manual/router/tools' }
      ]
    }
  ]
}

