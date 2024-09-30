<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            @if(count($respostas) > 0)
            <div style="width: 800px; height: 600px; margin: auto;">
                <canvas id="myChart"></canvas> <!-- Elemento Canvas para o gráfico -->
            </div>
        
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar', // Tipo de gráfico
                        data: {
                            labels: @json($labels), // Labels das perguntas
                            datasets: @json($datasets) // Dados para cada tipo de resposta
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>
            @endif
            <!--//-->
        </div>
    </div>
</x-educacional-layout>