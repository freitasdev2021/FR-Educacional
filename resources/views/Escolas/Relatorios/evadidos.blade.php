<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Escolas/Relatorios/Gerar',$Tipo)}}" method="POST">
                @csrf
                @method('PUT');
                <h5>Escolha os dados do relatório</h5>
                <div class="col-sm-12 p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Nome da Escola" name="Conteudo[]" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                          Nome da Escola
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Evadidos" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Evadidos
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="Endereço" name="Conteudo[]" id="flexCheckChecked">
                        <label class="form-check-label" for="flexCheckChecked">
                          Endereço
                        </label>
                    </div>
                </div>
                <hr>
                <button class="btn btn-primary col-auto">Gerar</button>
                <a href="{{route('Escolas/Relatorios')}}" class="btn btn-light col-auto">Voltar</a>
            </form>
        </div>
    </div>
</x-educacional-layout>