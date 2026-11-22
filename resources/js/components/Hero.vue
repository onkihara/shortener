<template>
  <div class="relative bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-200 dark:bg-indigo-900 opacity-20 rounded-full blur-3xl -mr-24 -mt-24"></div>
      <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-200 dark:bg-blue-900 opacity-20 rounded-full blur-3xl -ml-24 -mb-24"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Left column: Headline and CTA -->
        <div class="space-y-8">
          <div class="space-y-4">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
              {{ $t('tagline') }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-xl">
              {{ $t('hero_description') }}
            </p>
          </div>

          <!-- Features highlights -->
          <div class="space-y-3">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $t('qr_generation') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('qr_generation_desc') }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $t('youtube_advert_free') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('youtube_advert_free_desc') }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $t('free_access') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('free_access_desc') }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $t('link_management') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('link_management_desc') }}</p>
              </div>
            </div>
          </div>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <a
              :href="createRoute"
              class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl"
            >
              {{ $t('get_started') }}
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </a>
            <a
              v-if="!isAuthenticated"
              :href="loginRoute"
              class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg border-2 border-gray-200 dark:border-gray-700 transition-colors duration-200"
            >
              {{ $t('login') }}
            </a>
            <a
              v-else
              :href="dashboardRoute"
              class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg border-2 border-gray-200 dark:border-gray-700 transition-colors duration-200"
            >
              {{ $t('dashboard_link') }}
            </a>
          </div>
        </div>

        <!-- Right column: Illustration -->
        <div class="hidden lg:block relative h-96 md:h-full flex items-center justify-center">
          <div class="relative w-full h-full max-w-md mx-auto">
            <!-- Animated gradient cards -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl opacity-10 dark:opacity-5 blur-xl"></div>

            <!-- Card 1 -->
            <div class="absolute top-0 right-0 w-72 h-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform rotate-6 hover:rotate-3 transition-transform duration-300">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                  </svg>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">QR-Code</h3>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('qr_generation_short') }}</p>
            </div>

            <!-- Card 2 -->
            <div class="absolute bottom-8 left-0 w-72 h-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform -rotate-6 hover:-rotate-3 transition-transform duration-300">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                  </svg>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">{{ $t('free_access') }}</h3>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ $t('free_access_short') }}</p>
            </div>

            <!-- Center accent circle -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-indigo-500 rounded-full opacity-10 dark:opacity-5 blur-2xl"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isAuthenticated: {
    type: Boolean,
    default: false
  },
  loginRoute: {
    type: String,
    default: 'login'
  },
  dashboardRoute: {
    type: String,
    default: 'dashboard'
  },
  createRoute: {
    type: String,
    required: true
  }
})
</script>
