@php
    $paciente = $paciente ?? null;
    $mis_citas = $mis_citas ?? null;
    $availableSlots = $availableSlots ?? [];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Psicóloga Clínica</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mama.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-mama.png') }}?v=2">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 font-sans flex flex-col items-center pb-16">

    @if($paciente)
    <div class="w-full bg-gray-800 py-3 px-6 flex justify-between items-center mb-8 shadow-sm">
        <span class="text-gray-200 font-medium">Hola, {{ $paciente->name ?? '' }}</span>
        <a href="/salir" class="text-red-400 hover:text-red-300 font-medium transition-colors text-sm">Cerrar sesión</a>
    </div>
    @else
    <div class="w-full pt-16"></div>
    @endif
    
    <!-- Perfil -->
    <div class="w-32 h-32 rounded-full bg-gray-800 shadow flex items-center justify-center">
        <img src="{{ asset('images/perfil-mama.jpg') }}" alt="Dra. Sharon Gutiérrez" class="w-full h-full object-cover rounded-full shadow-lg border-4 border-amber-500/20">
    </div>
    
    <h1 class="text-3xl font-bold text-white mt-6">Sharon Gutiérrez Avendaño</h1>
    <h2 class="text-amber-500 font-medium tracking-wide mt-2">Psicóloga Clínica</h2>
    
    <div class="flex justify-center mt-6 mb-8">
        <img src="{{ asset('images/logo-mama.png') }}" alt="Logo Psicología Clínica" class="h-24 w-auto drop-shadow-md rounded-xl">
    </div>

    <!-- Biografía -->
    <p class="max-w-2xl text-center text-gray-400 mt-6 leading-relaxed px-4">
        Mi propósito es brindar un espacio de confianza y respeto donde cada persona pueda expresar sus sentimientos, identificar sus fortalezas y avanzar hacia una mejor calidad de vida. Mi compromiso es acompañar a cada persona con sensibilidad humana, responsabilidad y respeto por su bienestar emocional.
    </p>

    <div class="w-full max-w-lg px-4">
        @if($paciente && $mis_citas)
            <div class="mt-12 mb-8">
                <h3 class="text-xl text-amber-500 font-bold mb-4">Tus Citas Registradas</h3>
                @forelse($mis_citas as $cita)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-3 flex justify-between items-center">
                        <div>
                            <p class="text-white font-medium flex items-center gap-1.5">
                                <span class="text-gray-400 text-xs uppercase tracking-wider">Fecha:</span> {{ \Carbon\Carbon::parse($cita->date)->format('d/m/Y') }}
                            </p>
                            <p class="text-amber-400 text-sm font-medium flex items-center gap-1.5 mt-0.5">
                                <span class="text-gray-400 text-xs uppercase tracking-wider">Hora:</span> {{ \Carbon\Carbon::parse($cita->time_slot)->format('h:i A') }}
                            </p>
                        </div>
                        <div>
                            @php
                                $color = match($cita->status) {
                                    'pendiente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/50',
                                    'confirmada', 'completada' => 'bg-green-500/20 text-green-400 border-green-500/50',
                                    'cancelada' => 'bg-red-500/20 text-red-400 border-red-500/50',
                                    default => 'bg-gray-500/20 text-gray-400 border-gray-500/50'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $color }}">
                                {{ ucfirst($cita->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 bg-gray-800/50 rounded-xl border border-gray-700">Aún no tienes citas registradas.</p>
                @endforelse
            </div>
        @endif

        <!-- Formulario de Reserva -->
        <form method="POST" action="/agendar" class="mt-8 w-full">
            @csrf
            
            <div class="mb-4">
                @if($paciente)
                    <input type="text" name="name" value="{{ $paciente->name ?? '' }}" readonly class="w-full bg-gray-700 border border-gray-600 text-gray-400 rounded-lg px-4 py-3 focus:outline-none cursor-not-allowed">
                @else
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nombre Completo" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                @endif
                @error('name')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                @if($paciente)
                    <input type="tel" name="phone" value="{{ $paciente->phone ?? '' }}" readonly class="w-full bg-gray-700 border border-gray-600 text-gray-400 rounded-lg px-4 py-3 focus:outline-none cursor-not-allowed">
                @else
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Teléfono / WhatsApp" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                @endif
                @error('phone')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 mb-2 font-medium">Día de la cita</label>
                <input type="date" name="date" value="{{ old('date') }}" required min="{{ \Carbon\Carbon::today()->toDateString() }}" max="{{ \Carbon\Carbon::today()->addMonth()->toDateString() }}" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors [color-scheme:dark]">
                @error('date')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 mb-2 font-medium">Hora de la cita</label>
                <select name="time_slot" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    <option value="" disabled {{ old('time_slot') ? '' : 'selected' }}>Selecciona un horario</option>
                    <option value="15:00:00" {{ old('time_slot') == '15:00:00' ? 'selected' : '' }}>03:00 PM</option>
                    <option value="16:00:00" {{ old('time_slot') == '16:00:00' ? 'selected' : '' }}>04:00 PM</option>
                    <option value="17:00:00" {{ old('time_slot') == '17:00:00' ? 'selected' : '' }}>05:00 PM</option>
                    <option value="18:00:00" {{ old('time_slot') == '18:00:00' ? 'selected' : '' }}>06:00 PM</option>
                    <option value="19:00:00" {{ old('time_slot') == '19:00:00' ? 'selected' : '' }}>07:00 PM</option>
                </select>
                @error('time_slot')
                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-lg rounded-lg px-4 py-4 transition-colors shadow-lg shadow-amber-500/20">
                Solicitar Cita
            </button>
        </form>

        @if(!$paciente)
        <div class="bg-gray-800/50 border border-gray-700 p-6 rounded-xl mt-12 text-center">
            <h3 class="text-white font-semibold mb-4">¿Ya eres paciente? Revisa tus citas</h3>
            <form method="POST" action="/acceder">
                @csrf
                <input type="tel" name="phone" placeholder="Tu número de teléfono" required class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 mb-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg px-4 py-3 transition-colors">
                    Entrar con mi número
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- SECCIÓN DE SERVICIOS PROFESIONALES -->
    <div class="mt-20 w-full max-w-4xl border-t border-gray-800 pt-16 px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-white tracking-tight">Atención Clínica y Psicoterapia</h2>
            <p class="text-amber-500 font-medium mt-2">Áreas de especialidad y servicios disponibles</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Servicio 1 -->
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl p-6 hover:border-amber-500/50 transition duration-300">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-100 mb-2">Terapia Psicológica</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Sesiones de terapia individual, de pareja y terapia familiar, diseñadas para abordar conflictos y mejorar las dinámicas relacionales.</p>
            </div>

            <!-- Servicio 2 -->
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl p-6 hover:border-[#3565fd]/50 transition duration-300">
                <div class="w-12 h-12 bg-[#3565fd]/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#3565fd]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-100 mb-2">Evaluación y Diagnóstico</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Emisión de diagnósticos clínicos precisos, aplicación de test psicológicos y test vocacionales para orientación profesional y personal.</p>
            </div>

            <!-- Servicio 3 -->
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl p-6 hover:border-[#3565fd]/50 transition duration-300">
                <div class="w-12 h-12 bg-[#3565fd]/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#3565fd]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-100 mb-2">Dificultades de Aprendizaje</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Atención especializada en problemas de aprendizaje y terapia de lenguaje, brindando herramientas para el desarrollo cognitivo.</p>
            </div>

            <!-- Servicio 4 -->
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl p-6 hover:border-amber-500/50 transition duration-300">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-100 mb-2">Constancias Psicológicas</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Emisión de constancias psicológicas oficiales para trámites legales, incluyendo portación de armas y licencias de conducir.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            icon: 'success',
            background: '#1f2937', 
            color: '#f3f4f6', 
            confirmButtonColor: '#f59e0b',
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            title: '¡Atención!',
            text: "{{ session('error') }}",
            icon: 'error',
            background: '#1f2937', 
            color: '#f3f4f6', 
            confirmButtonColor: '#ef4444',
        });
    </script>
    @endif
</body>
</html>
