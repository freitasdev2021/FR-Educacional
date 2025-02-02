<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            
            <form class="p-2" action="{{route('Alunos/FichaIndividual',$id)}}" method="POST">
                @csrf
                @method("PATCH")
                @foreach($Disciplinas as $d)
                    <h3>{{$d->Disciplina}}</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr align="center">
                                <th rowspan="2">Ord.</th>
                                <th rowspan="2">Indicadores</th>
                                <th colspan="4">Bimestres/Conceitos</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>1 BIM</th>
                                <th>2 BIM</th>
                                <th>3 BIM</th>
                                <th>4 BIM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(json_decode($d->Sinteses) as $key => $s)
                                @if(!empty($s->Sintese))
                                    <tr class="{{$d->Disciplina}}_{{$key+1}}">
                                        <input type="hidden" value="{{$d->Disciplina}}" name="Disciplina[]">
                                        <td>{{$key+1}}</td>
                                        <td>{{$s->Sintese}}</td>
                                        <input name="Sintese[]" type="hidden" value="{{$s->Sintese}}">
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" name="1BIM[]" data-disciplina="{{$d->Disciplina}}" data-sintese="{{$s->Sintese}}" value="{{isset($Ficha[$d->Disciplina][$s->Sintese]['1 BIM']) ? $Ficha[$d->Disciplina][$s->Sintese]['1 BIM'] : ''}}"></td>
                                        <td><input type="text" name="2BIM[]" data-disciplina="{{$d->Disciplina}}" data-sintese="{{$s->Sintese}}" value="{{isset($Ficha[$d->Disciplina][$s->Sintese]['2 BIM']) ? $Ficha[$d->Disciplina][$s->Sintese]['2 BIM'] : ''}}"></td>
                                        <td><input type="text" name="3BIM[]" data-disciplina="{{$d->Disciplina}}" data-sintese="{{$s->Sintese}}" value="{{isset($Ficha[$d->Disciplina][$s->Sintese]['3 BIM']) ? $Ficha[$d->Disciplina][$s->Sintese]['3 BIM'] : ''}}"></td>
                                        <td><input type="text" name="4BIM[]" data-disciplina="{{$d->Disciplina}}" data-sintese="{{$s->Sintese}}" value="{{isset($Ficha[$d->Disciplina][$s->Sintese]['4 BIM']) ? $Ficha[$d->Disciplina][$s->Sintese]['4 BIM'] : ''}}"></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                <input type="hidden" name="Ficha">
                <div class="col-sm-12">
                    <label>Observações</label>
                    <textarea name="Observacoes" class="form-control"></textarea>
                </div>
                <div class="col-sm-12">
                    <label>Parecer Descritivo</label>
                    <textarea name="Parecer" class="form-control"></textarea>
                </div>
                <br>
                <div class="col-auto">
                    <button type="submit" class="btn btn-default">Gerar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $("form").on("submit",function(e){

            var data = {};

            $('tbody tr').each(function() {
                var disciplina = $(this).find('input[name="Disciplina[]"]').val();
                var sintese = $(this).find('input[name="Sintese[]"]').val();
                var bim1 = $(this).find('input[name="1BIM[]"]').val();
                var bim2 = $(this).find('input[name="2BIM[]"]').val();
                var bim3 = $(this).find('input[name="3BIM[]"]').val();
                var bim4 = $(this).find('input[name="4BIM[]"]').val();

                if (!data[disciplina]) {
                    data[disciplina] = {};
                }

                data[disciplina][sintese] = {
                    "1 BIM": bim1,
                    "2 BIM": bim2,
                    "3 BIM": bim3,
                    "4 BIM": bim4
                };
            });

            $("input[name=Ficha]").val(JSON.stringify(data));
        })
    </script>
</x-educacional-layout>