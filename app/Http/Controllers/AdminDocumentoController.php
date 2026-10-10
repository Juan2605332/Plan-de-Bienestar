<?php

namespace App\Http\Controllers;

use App\Models\EventoDocumento;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentoController extends Controller
{
    public function descargar(EventoDocumento $documento): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($documento->ruta), 404);

        return Storage::disk('local')->download($documento->ruta, $documento->nombre_original);
    }
}
