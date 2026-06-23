import os
import re
from pathlib import Path

components_dir = Path(r"c:\laragon\www\receivingpkt\resources\views\components\frontend")
components_dir.mkdir(parents=True, exist_ok=True)

# 1. READ HOME FILE (using the user's latest paste in ⚡home.blade.php)
home_path = r"c:\laragon\www\receivingpkt\resources\views\components\⚡home.blade.php"
with open(home_path, "r", encoding="utf-8") as f:
    home_content = f.read()

# EXTRACT NAVBAR
# It starts at <nav x-data="{ mobileMenuOpen: false }" and ends at </nav>
nav_match = re.search(r'<nav.*?</nav>', home_content, re.DOTALL)
navbar_html = nav_match.group(0) if nav_match else ""

# EXTRACT HERO
# It starts at <main and ends at </main>
main_match = re.search(r'<main.*?</main>', home_content, re.DOTALL)
hero_html = main_match.group(0) if main_match else ""

# EXTRACT STYLES AND BACKGROUND BLOBS
blobs_match = re.search(r'<!-- Background Texture -->.*?</div>\s*</div>', home_content, re.DOTALL)
blobs_html = blobs_match.group(0) if blobs_match else ""

styles_match = re.search(r'<style>.*?</style>', home_content, re.DOTALL)
styles_html = styles_match.group(0) if styles_match else ""

# Function to apply dark theme classes (re-using our previous logic)
def apply_dark_classes(html):
    replacements = {
        'text-slate-300': 'text-slate-600 dark:text-slate-300',
        'text-slate-400': 'text-slate-500 dark:text-slate-400',
        'text-white': 'text-slate-800 dark:text-white',
        'bg-[#051F34]/60': 'bg-white/80 dark:bg-[#051F34]/60 md:bg-white/70 md:dark:bg-[#051F34]/60',
        'bg-[#051F34]/90': 'bg-white/95 dark:bg-[#051F34]/90',
        'bg-[#051F34]/50': 'bg-slate-50/90 dark:bg-[#051F34]/50',
        'bg-[#031525]/80': 'bg-slate-100/80 dark:bg-[#031525]/80',
        'bg-white/5': 'bg-slate-50 dark:bg-white/5',
        'bg-white/10': 'bg-slate-100 dark:bg-white/10',
        'border-white/10': 'border-slate-200 dark:border-white/10',
        'border-white/20': 'border-slate-300 dark:border-white/20',
        # Logo logic
        '''<img src="{{ asset('images/logo/receiving_white.png') }}"''': '''<img :src="isDark ? '{{ asset('images/logo/receiving_white.png') }}' : '{{ asset('images/logo/receiving_dark.png') }}'"''',
        # Force some text to dark mode aware
        'hover:text-white': 'hover:text-[#0A4F86] dark:hover:text-white',
    }
    
    # We want to be careful with text-white on buttons that are already colored (like the orange gradient).
    # We will let the button text stay white.
    # Let's use a simpler mapping for the specific glassmorphism elements
    
    # The current code the user pasted is HARDCODED dark mode.
    # We need to inject `dark:` prefix for the dark styles and `slate/white` for the light ones.
    
    # For NAVBAR:
    html = html.replace('bg-[#051F34]/60', 'bg-white/80 dark:bg-[#051F34]/60')
    html = html.replace('bg-[#051F34]/90', 'bg-white/95 dark:bg-[#051F34]/90')
    html = html.replace('bg-[#051F34]/50', 'bg-slate-100/95 dark:bg-[#051F34]/50')
    html = html.replace('bg-[#031525]/80', 'bg-slate-200/80 dark:bg-[#031525]/80')
    html = html.replace('text-slate-300', 'text-slate-600 dark:text-slate-300')
    html = html.replace('text-slate-400', 'text-slate-500 dark:text-slate-400')
    html = html.replace('text-slate-200', 'text-slate-700 dark:text-slate-200')
    html = html.replace('border-white/10', 'border-slate-200 dark:border-white/10')
    html = html.replace('bg-white/5', 'bg-slate-50 dark:bg-white/5')
    html = html.replace('hover:bg-white/5', 'hover:bg-slate-100 dark:hover:bg-white/5')
    html = html.replace('hover:bg-white/10', 'hover:bg-slate-200 dark:hover:bg-white/10')
    
    html = html.replace(
        '''<img src="{{ asset('images/logo/receiving_white.png') }}"''',
        '''<img :src="isDark ? '{{ asset('images/logo/receiving_white.png') }}' : '{{ asset('images/logo/receiving_dark.png') }}'"'''
    )
    
    return html

# Make Navbar
navbar_html = apply_dark_classes(navbar_html)

# Add Dark/Light Toggle button to Navbar (near Akses Sistem)
toggle_btn = """
                <!-- Theme Toggler -->
                <button @click="isDark = !isDark" class="p-2 rounded-xl transition-all duration-300 bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-yellow-400 hover:bg-slate-300 dark:hover:bg-white/20">
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!isDark" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
"""
navbar_html = navbar_html.replace('<div class="flex items-center gap-4">', f'<div class="flex items-center gap-2 md:gap-4">\n{toggle_btn}')


with open(components_dir / "navbar.blade.php", "w", encoding="utf-8") as f:
    f.write(navbar_html)

# Make Hero
hero_html = apply_dark_classes(hero_html)
# Ensure text-white doesn't get blindly replaced where it shouldn't be
hero_html = hero_html.replace('text-white mb-6', 'text-slate-800 dark:text-white mb-6')
hero_html = hero_html.replace('font-black text-white', 'font-black text-slate-800 dark:text-white')

with open(components_dir / "hero.blade.php", "w", encoding="utf-8") as f:
    f.write(blobs_html + "\n" + hero_html)

# Make Footer
footer_html = """
<footer class="relative z-10 py-6 border-t border-slate-200 dark:border-white/10 bg-white/50 dark:bg-[#031525]/50 backdrop-blur-md mt-auto">
    <div class="container mx-auto px-5 md:px-12 lg:px-24 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-xs text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} PT Pupuk Kalimantan Timur. All rights reserved.
        </p>
        <div class="flex items-center gap-4">
            <a href="#" class="text-xs text-slate-500 dark:text-slate-400 hover:text-[#0A4F86] dark:hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="text-xs text-slate-500 dark:text-slate-400 hover:text-[#0A4F86] dark:hover:text-white transition-colors">Terms of Service</a>
        </div>
    </div>
</footer>
"""
with open(components_dir / "footer.blade.php", "w", encoding="utf-8") as f:
    f.write(footer_html)

# 2. WRITE HOME.BLADE.PHP (the clean version)
new_home_content = f"""<?php

use Livewire\\Attributes\\Layout;
use Livewire\\Attributes\\Title;
use Livewire\\Component;

new #[Layout('components.layouts.frontend')] #[Title('Welcome - Receiving PKT')] class extends Component {{
    // Logika komponen Anda di sini
}};
?>

<div class="relative w-full min-h-screen selection:bg-[#F47920] selection:text-white overflow-hidden">
    <livewire:chatbot-widget />
    
{styles_html}

    <x-frontend.navbar />
    
    <x-frontend.hero />

    <x-frontend.footer />
</div>
"""

with open(r"c:\laragon\www\receivingpkt\resources\views\components\home.blade.php", "w", encoding="utf-8") as f:
    f.write(new_home_content)

# We will also overwrite ⚡home.blade.php just so the user sees the change there if they are looking at it
with open(home_path, "w", encoding="utf-8") as f:
    f.write(new_home_content)


# 3. REFACTOR LAYOUT
layout_path = r"c:\laragon\www\receivingpkt\resources\views\components\layouts\frontend.blade.php"
with open(layout_path, "r", encoding="utf-8") as f:
    layout_content = f.read()

new_body_tag = """<body x-data="{ isDark: localStorage.getItem('theme') ? localStorage.getItem('theme') === 'dark' : true }" 
      x-init="$watch('isDark', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); })"
      :class="isDark ? 'dark' : ''"
      class="bg-slate-50 dark:bg-[#031525] text-slate-800 dark:text-slate-200 antialiased overflow-x-hidden transition-colors duration-500 flex flex-col min-h-screen">"""

layout_content = re.sub(r'<body.*?>', new_body_tag, layout_content)

with open(layout_path, "w", encoding="utf-8") as f:
    f.write(layout_content)

print("Refactoring completed successfully")
