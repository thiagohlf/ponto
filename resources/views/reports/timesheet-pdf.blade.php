<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espelho de Ponto - {{ $employee->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.1;
            margin: 0;
            padding: 8px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
        }
        
        .company-info {
            margin-bottom: 2px;
        }
        
        .company-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .company-details {
            font-size: 7px;
            color: #666;
            line-height: 1.0;
        }
        
        .document-title {
            font-size: 10px;
            font-weight: bold;
            margin: 4px 0 2px 0;
            text-transform: uppercase;
        }
        
        .period {
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        .employee-section {
            margin: 4px 0;
            border: 1px solid #ddd;
            padding: 4px;
            background-color: #f9f9f9;
        }
        
        .employee-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1px;
        }
        
        .employee-info {
            display: table;
            width: 100%;
        }
        
        .employee-row {
            display: table-row;
        }
        
        .employee-label {
            display: table-cell;
            width: 25%;
            font-weight: bold;
            padding: 1px 5px 1px 0;
            vertical-align: top;
            font-size: 7px;
        }
        
        .employee-value {
            display: table-cell;
            padding: 1px 0;
            vertical-align: top;
            font-size: 7px;
        }
        
        .records-section {
            margin: 3px 0;
        }
        
        .records-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 1px;
        }
        
        .timesheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        
        .timesheet-table th {
            background-color: #f0f0f0;
            padding: 2px 1px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #333;
            font-size: 7px;
        }
        
        .timesheet-table td {
            padding: 1px;
            border: 1px solid #333;
            text-align: center;
            font-size: 7px;
            height: 12px;
        }
        
        .timesheet-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .day-cell {
            font-weight: bold;
            background-color: #f8f8f8;
            text-align: left;
            padding-left: 2px;
        }
        
        .weekend {
            background-color: #ffe6e6;
        }
        
        .time-cell {
            font-family: monospace;
            font-weight: bold;
        }
        
        .record-type {
            font-weight: bold;
        }
        
        .record-type.entry {
            color: #28a745;
        }
        
        .record-type.exit {
            color: #dc3545;
        }
        
        .record-type.meal {
            color: #007bff;
        }
        
        .status-valid {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-invalid {
            color: #dc3545;
            font-weight: bold;
        }
        
        .summary-section {
            margin: 2px 0;
            padding: 2px;
            font-size: 6px;
            background-color: #f9f9f9;
        }
        
        .summary-title {
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 1px;
            color: #333;
        }
        
        .summary-info {
            display: inline-block;
            width: 100%;
        }
        
        .summary-row {
            display: inline;
            margin-right: 10px;
        }
        
        .summary-label {
            font-weight: bold;
            font-size: 6px;
        }
        
        .summary-value {
            font-size: 6px;
        }
        
        .signature-section {
            margin-top: 4px;
            page-break-inside: avoid;
        }
        
        .signature-title {
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
        }
        
        .signature-boxes {
            display: table;
            width: 100%;
            margin-top: 3px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 5px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 1px;
            height: 15px;
        }
        
        .signature-label {
            font-size: 6px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 3px;
            text-align: center;
            font-size: 5px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 2px;
        }
        
        .no-records {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company->name }}</div>
            @if($company->trade_name && $company->trade_name !== $company->name)
                <div style="font-size: 9px; margin-bottom: 1px;">{{ $company->trade_name }}</div>
            @endif
            <div class="company-details">
                CNPJ: {{ $company->cnpj ?? 'N/A' }} | 
                {{ $company->address ?? '' }}{{ $company->number ? ', ' . $company->number : '' }}{{ $company->complement ? ', ' . $company->complement : '' }}<br>
                {{ $company->neighborhood ?? '' }}{{ $company->city ? ' - ' . $company->city : '' }}{{ $company->state ? '/' . $company->state : '' }}{{ $company->zip_code ? ' - CEP: ' . $company->zip_code : '' }}<br>
                @if($company->phone)Telefone: {{ $company->phone }}@endif
                @if($company->email && $company->phone) | @endif
                @if($company->email)E-mail: {{ $company->email }}@endif
            </div>
        </div>
        
        <div class="document-title">Espelho de Ponto</div>
        <div class="period">
            Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}
        </div>
    </div>

    <!-- Dados do Funcionário -->
    <div class="employee-section">
        <div class="employee-title">Dados do Funcionário</div>
        <div class="employee-info">
            <div class="employee-row">
                <div class="employee-label">Nome:</div>
                <div class="employee-value">{{ $employee->name }}</div>
            </div>
            <div class="employee-row">
                <div class="employee-label">CPF:</div>
                <div class="employee-value">{{ $employee->cpf ?? 'N/A' }}</div>
            </div>
            <div class="employee-row">
                <div class="employee-label">Matrícula:</div>
                <div class="employee-value">{{ $employee->registration_number ?? 'N/A' }}</div>
            </div>
            <div class="employee-row">
                <div class="employee-label">Cargo:</div>
                <div class="employee-value">{{ $employee->position ?? 'N/A' }}</div>
            </div>
            <div class="employee-row">
                <div class="employee-label">Admissão:</div>
                <div class="employee-value">{{ $employee->admission_date ? $employee->admission_date->format('d/m/Y') : 'N/A' }}</div>
            </div>
            @if($employee->department)
            <div class="employee-row">
                <div class="employee-label">Departamento:</div>
                <div class="employee-value">{{ $employee->department->name }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Registros de Ponto -->
    <div class="records-section">
        <div class="records-title">Registros de Ponto</div>
        
        @if($timesheetData->count() > 0)
            <table class="timesheet-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Dia</th>
                        <th style="width: 16%;">Entrada</th>
                        <th style="width: 16%;">Saída Almoço</th>
                        <th style="width: 16%;">Retorno Almoço</th>
                        <th style="width: 16%;">Saída</th>
                        <th style="width: 16%;">Observações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timesheetData as $dayData)
                        <tr class="{{ $dayData['is_weekend'] ? 'weekend' : '' }}">
                            <td class="day-cell">
                                {{ $dayData['day'] }}/{{ $dayData['month'] }} - {{ $dayData['weekday'] }}
                            </td>
                            <td class="time-cell">
                                {{ $dayData['entry'] ?? '' }}
                            </td>
                            <td class="time-cell">
                                {{ $dayData['meal_start'] ?? '' }}
                            </td>
                            <td class="time-cell">
                                {{ $dayData['meal_end'] ?? '' }}
                            </td>
                            <td class="time-cell">
                                {{ $dayData['exit'] ?? '' }}
                            </td>
                            <td style="font-size: 6px;">
                                @if($dayData['has_manual'])
                                    <span style="color: #ff6600;">M</span>
                                @endif
                                @if($dayData['has_pending'])
                                    <span style="color: #ffc107;">P</span>
                                @endif
                                @if($dayData['has_invalid'])
                                    <span style="color: #dc3545;">I</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-records">
                Nenhum registro de ponto encontrado para o período selecionado.
            </div>
        @endif
    </div>

    <!-- Resumo -->
    @if($timeRecords->count() > 0)
    <div class="summary-section">
        <div class="summary-title">Resumo do Período</div>
        <div class="summary-info">
            <div class="summary-row">
                <div class="summary-label">Total de Registros:</div>
                <div class="summary-value">{{ $timeRecords->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Registros Válidos:</div>
                <div class="summary-value">{{ $timeRecords->where('status', 'valid')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Registros Pendentes:</div>
                <div class="summary-value">{{ $timeRecords->where('status', 'pending_approval')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Registros Manuais:</div>
                <div class="summary-value">{{ $timeRecords->where('identification_method', 'manual')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Dias com Registros:</div>
                <div class="summary-value">{{ $timesheetData->where('records_count', '>', 0)->count() }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Seção de Assinaturas -->
    <div class="signature-section">
        <div class="signature-title">Conferência e Assinaturas</div>
        
        <div class="signature-boxes">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Funcionário</div>
                <div style="font-size: 10px; margin-top: 5px;">{{ $employee->name }}</div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Responsável pelo RH</div>
                <div style="font-size: 10px; margin-top: 5px;">Nome e Assinatura</div>
            </div>
        </div>
        
        <div style="margin-top: 2px; font-size: 5px; text-align: justify; color: #666;">
            <strong>Declaração:</strong> Declaro que os registros de ponto acima conferem com minha jornada de trabalho no período indicado. 
            Estou ciente de que qualquer irregularidade deve ser comunicada imediatamente ao setor de Recursos Humanos.
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <div>Documento gerado automaticamente em {{ $generatedAt->format('d/m/Y H:i:s') }}</div>
        <div style="margin-top: 5px;">
            Este documento é válido apenas com as assinaturas do funcionário e do responsável pelo RH
        </div>
    </div>
</body>
</html>