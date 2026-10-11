<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;

Route::get('/', function () {
    $phone = session('patient_phone');
    $paciente = null;
    $mis_citas = null;

    if ($phone) {
        $paciente = Patient::where('phone', $phone)->first();
        if ($paciente) {
            $mis_citas = $paciente->appointments()->orderBy('date', 'desc')->get();
        }
    }

    return view('welcome', compact('paciente', 'mis_citas'));
});

Route::post('/agendar', function (Request $request) {
    $request->validate([
        'name' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|min:3|max:100',
        'phone' => 'required|numeric|digits_between:8,15',
        'date' => 'required|date|after_or_equal:today|before_or_equal:+1 month',
        'time_slot' => 'required',
    ], [
        'name.required' => 'El nombre es obligatorio.',
        'name.regex' => 'El nombre solo puede contener letras y espacios.',
        'name.min' => 'El nombre debe tener al menos 3 caracteres.',
        'name.max' => 'El nombre es demasiado largo (máximo 100 caracteres).',
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.numeric' => 'El teléfono solo debe contener números (sin espacios ni símbolos).',
        'phone.digits_between' => 'El teléfono debe tener entre 8 y 15 dígitos.',
        'date.required' => 'Debes seleccionar un día para la cita.',
        'date.after_or_equal' => 'No puedes seleccionar fechas pasadas.',
        'date.before_or_equal' => 'Solo puedes agendar con máximo 1 mes de anticipación.',
        'time_slot.required' => 'Debes seleccionar la hora de la cita.',
    ]);

    $ocupado = Appointment::where('date', $request->date)
        ->where('time_slot', $request->time_slot)
        ->whereIn('status', ['pendiente', 'confirmada', 'completada'])
        ->exists();

    if ($ocupado) {
        return back()->with('error', 'Este horario ya está reservado. Por favor, elige otra fecha u hora.');
    }

    $patient = Patient::firstOrCreate(
        ['phone' => $request->phone],
        ['name' => $request->name]
    );

    $activeAppointmentsCount = Appointment::where('patient_id', $patient->id)
        ->whereIn('status', ['pendiente', 'confirmada'])
        ->count();

    if ($activeAppointmentsCount >= 10) {
        return back()->with('error', 'Has alcanzado el límite máximo de 10 citas agendadas. Si necesitas atención adicional, por favor contacta a la doctora por WhatsApp.');
    }

    Appointment::create([
        'patient_id' => $patient->id,
        'date' => $request->date,
        'time_slot' => $request->time_slot,
        'status' => 'pendiente',
    ]);

    session(['patient_phone' => $patient->phone]);

    return back()->with('success', '¡Cita solicitada con éxito! La doctora se pondrá en contacto contigo a la brevedad.');
});

Route::post('/acceder', function (Request $request) {
    $request->validate([
        'phone' => 'required|string|max:255',
    ]);

    $patient = Patient::where('phone', $request->phone)->first();

    if ($patient) {
        session(['patient_phone' => $patient->phone]);
        return back()->with('success', '¡Bienvenido/a de nuevo! Aquí puedes consultar el estado de tus citas.');
    }

    return back()->with('error', 'No encontramos ningún expediente asociado a este número. Puedes agendar tu primera cita arriba.');
});

Route::get('/salir', function () {
    session()->forget('patient_phone');
    return back();
});
