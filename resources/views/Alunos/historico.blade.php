<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header img {
                    width: 100px;
                    height: 100px;
                }
                .header h1 {
                    margin: 10px 0 5px;
                    font-size: 24px;
                    color: #333;
                }
                .header h2 {
                    margin: 5px 0;
                    font-size: 20px;
                    color: #666;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                table, th, td {
                    border: 1px solid #ccc;
                }
                th, td {
                    padding: 10px;
                    text-align: center;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
            <table>
                <tr>
                    <th>Nome</th>
                    <td>João da Silva</td>
                </tr>
                <tr>
                    <th>Matrícula</th>
                    <td>2023123456</td>
                </tr>
                <tr>
                    <th>Data de Nascimento</th>
                    <td>15/03/2005</td>
                </tr>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>2019</th>
                        <th>2020</th>
                        <th>2021</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Matemática</td>
                        <td>8.0</td>
                        <td>7.5</td>
                        <td>8.5</td>
                    </tr>
                    <tr>
                        <td>Português</td>
                        <td>7.5</td>
                        <td>7.0</td>
                        <td>8.0</td>
                    </tr>
                    <tr>
                        <td>História</td>
                        <td>9.0</td>
                        <td>8.5</td>
                        <td>9.0</td>
                    </tr>
                    <tr>
                        <td>Geografia</td>
                        <td>6.5</td>
                        <td>7.0</td>
                        <td>7.5</td>
                    </tr>
                    <tr>
                        <td>Ciências</td>
                        <td>7.0</td>
                        <td>8.0</td>
                        <td>8.5</td>
                    </tr>
                    <tr>
                        <td>Inglês</td>
                        <td>8.0</td>
                        <td>7.5</td>
                        <td>8.0</td>
                    </tr>
                    <tr>
                        <td>Educação Física</td>
                        <td>9.5</td>
                        <td>9.0</td>
                        <td>9.5</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-educacional-layout>