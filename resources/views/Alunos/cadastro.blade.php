<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <form action="{{route('Alunos/Save')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(session('success'))
                <div class="col-sm-12 shadow p-2 bg-success text-white">
                   <strong>{{session('success')}}</strong>
                </div>
                @elseif(session('error'))
                <div class="col-sm-12 shadow p-2 bg-danger text-white">
                   <strong>{{session('error')}}</strong>
                </div>
                <br>
                @endif
                @if(isset($Registro->IDAluno) && isset($Registro->IDMatricula))
                <input type="hidden" value="{{$Registro->IDAluno}}" name="IDAluno">
                <input type="hidden" value="{{$Registro->IDMatricula}}" name="IDMatricula">
                <input type="hidden" name="oldFoto" value="{{$Registro->Foto}}">
                <input type="hidden" name="CDPasta" value="{{$Registro->CDPasta}}">
                @endif
                <div class="col-sm-12 p-2">
                @if(in_array(Auth::user()->tipo,[4,4.5,2,2.5]))
                    <div>
                        <div class="d-flex justify-content-center mb-4">
                            <img id="selectedAvatar" src="{{ isset($Registro->Foto) ? url('storage/organizacao_' . Auth::user()->id_org . '_alunos/aluno_' . $Registro->CDPasta . '/' . $Registro->Foto) : asset('img/kidAvatar.png') }}"
                            class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" alt="example placeholder" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <div data-mdb-ripple-init class="btn btn-primary btn-rounded">
                                <label class="form-label text-white m-1" for="customFile2">Adicionar Foto</label>
                                <input type="file" name="Foto" class="form-control d-none" id="customFile2" onchange="displaySelectedImage(event, 'selectedAvatar')" accept="image/jpg,image/png,image/jpeg" {{!isset($Registro) ? '' : ''}} />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" name="Nome" value="{{isset($Registro->Nome) ? $Registro->Nome : ''}}" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RG) ? $Registro->RG : ''}}" name="RG"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPF) ? $Registro->CPF : ''}}" name="CPF"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Email) ? $Registro->Email : ''}}" name="Email"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nascimento<strong class="text-danger">*</strong></label>
                            <input type="date" class="form-control" value="{{isset($Registro->Nascimento) ? $Registro->Nascimento : ''}}" name="Nascimento" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Cartão do SUS</label>
                            <input type="text" class="form-control" value="{{isset($Registro->SUS) ? $Registro->SUS : ''}}" name="SUS"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Passaporte</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Passaporte) ? $Registro->Passaporte : ''}}" name="Passaporte"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CNH</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CNH) ? $Registro->CNH : ''}}" name="CNH"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Naturalidade</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Naturalidade) ? $Registro->Naturalidade : ''}}" name="Naturalidade"> 
                        </div>
                        <div class="col-sm-2">
                            <label>NIS</label>
                            <input type="number" class="form-control" value="{{isset($Registro->NIS) ? $Registro->NIS : ''}}" name="NIS"> 
                        </div>
                        <div class="col-sm-2">
                            <label>INEP</label>
                            <input type="number" class="form-control" value="{{isset($Registro->INEP) ? $Registro->INEP : ''}}" name="INEP"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Orgão Exp</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Expedidor) ? $Registro->Expedidor : ''}}" name="Expedidor"> 
                        </div>
                        <div class="col-sm-2">
                            <label>C.Nascimento</label>
                            <input type="text" class="form-control" value="{{isset($Registro->CNascimento) ? $Registro->CNascimento : ''}}" name="CNascimento"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Nacionalidade</label>
                            <input type="text" class="form-control" value="{{isset($Registro->Nacionalidade) ? $Registro->Nacionalidade : ''}}" name="Nacionalidade"> 
                        </div>
                    </div>
                    <hr>
                    <h5>Dados do Responsável</h5>
                    <div class="row">
                        <div class="col-sm-2">
                        <label>Responsavel<strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control" value="{{isset($Registro->NMResponsavel) ? $Registro->NMResponsavel : ''}}" name="NMResponsavel" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Registro->RGPais) ? $Registro->RGPais : ''}}" name="RGPais"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->CPFResponsavel) ? $Registro->CPFResponsavel : ''}}" name="CPFResponsavel" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="email" class="form-control" value="{{isset($Registro->EmailResponsavel) ? $Registro->EmailResponsavel : ''}}" name="EmailResponsavel"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Profissão<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->Profissao) ? $Registro->Profissao : ''}}" name="Profissao" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Registro->Escolaridade) ? $Registro->Escolaridade : ''}}" name="Escolaridade" required> 
                        </div>
                    </div>
                    <hr>
                    <h5>Dados dos Pais</h5>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Pai</label>
                            <input type="text" class="form-control" value="{{isset($Pais->Pai) ? $Pais->Pai : ''}}" name="Pai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Pais->RGPai) ? $Pais->RGPai : ''}}" name="RGPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Pais->CPFPai) ? $Pais->CPFPai : ''}}" name="CPFPai"> 
                        </div>
                        <div class="col-sm-3">
                            <label>Profissão</label>
                            <input type="text" class="form-control" value="{{isset($Pais->ProfissaoPai) ? $Pais->ProfissaoPai : ''}}" name="ProfissaoPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade</label>
                            <input type="text" class="form-control" value="{{isset($Pais->EscolaridadePai) ? $Pais->EscolaridadePai : ''}}" name="EscolaridadePai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Data de Nascimento</label>
                            <input type="text" class="form-control" value="{{isset($Pais->NascimentoPai) ? $Pais->NascimentoPai : ''}}" name="NascimentoPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{isset($Pais->EmailPai) ? $Pais->EmailPai : ''}}" name="EmailPai"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Telefone</label>
                            <input type="text" class="form-control" value="{{isset($Pais->TelefonePai) ? $Pais->TelefonePai : ''}}" name="TelefonePai"> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Mãe<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->Mae) ? $Pais->Mae : ''}}" name="Mae" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>RG</label>
                            <input type="text" class="form-control" value="{{isset($Pais->RGMae) ? $Pais->RGMae : ''}}" name="RGMae"> 
                        </div>
                        <div class="col-sm-2">
                            <label>CPF</label>
                            <input type="text" class="form-control" value="{{isset($Pais->CPFMae) ? $Pais->CPFMae : ''}}" name="CPFMae"> 
                        </div>
                        <div class="col-sm-3">
                            <label>Profissão<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->ProfissaoMae) ? $Pais->ProfissaoMae : ''}}" name="ProfissaoMae" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Escolaridade<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" value="{{isset($Pais->EscolaridadeMae) ? $Pais->EscolaridadeMae : ''}}" name="EscolaridadeMae" required> 
                        </div>
                        <div class="col-sm-2">
                            <label>Data de Nascimento</label>
                            <input type="text" class="form-control" value="{{isset($Pais->NascimentoMae) ? $Pais->NascimentoMae : ''}}" name="NascimentoMae"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{isset($Pais->EmailMae) ? $Pais->EmailMae : ''}}" name="EmailMae"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Telefone</label>
                            <input type="text" class="form-control" value="{{isset($Pais->TelefoneMae) ? $Pais->TelefoneMae : ''}}" name="TelefoneMae"> 
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>CEP<strong class="text-danger">*</strong></label>
                            <input type="text" name="CEP" class="form-control" required value="{{isset($Registro->CEP) ? $Registro->CEP : ''}}" required>
                        </div>
                        <div class="col-sm-5">
                            <label>Rua<strong class="text-danger">*</strong></label>
                            <input type="text" name="Rua" class="form-control" maxlength="50" value="{{isset($Registro->Rua) ? $Registro->Rua : ''}}" required>
                        </div>
                        <div class="col-sm-3">
                            <label>Bairro<strong class="text-danger">*</strong></label>
                            <input type="text" name="Bairro" class="form-control" maxlength="50" value="{{isset($Registro->Bairro) ? $Registro->Bairro : ''}}" minlength="2" required>
                        </div>
                        <div class="col-sm-1">
                            <label>UF<strong class="text-danger">*</strong></label>
                            <input type="text" name="UF" class="form-control" maxlength="2" value="{{isset($Registro->UF) ? $Registro->UF : ''}}" required>
                        </div>
                        <div class="col-sm-1">
                            <label>Numero</label>
                            <input type="text" name="Numero" class="form-control" maxlength="4" value="{{isset($Registro->Numero) ? $Registro->Numero : '0'}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Cidade<strong class="text-danger">*</strong></label>
                            <input type="text" name="Cidade" class="form-control" maxlength="50" value="{{isset($Registro->Cidade) ? $Registro->Cidade : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Responsavel<strong class="text-danger">*</strong></label>
                            <input type="text" name="CLResponsavel" class="form-control" maxlength="50" value="{{isset($Registro->CLResponsavel) ? $Registro->CLResponsavel : ''}}" minlength="3" required>
                        </div>
                        <div class="col-sm-4">
                            <label>Celular Aluno</label>
                            <input type="text" name="Celular" class="form-control" maxlength="50" value="{{isset($Registro->Celular) ? $Registro->Celular : ''}}" minlength="3">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Turma<strong class="text-danger">*</strong></label>
                            <select class="form-control" name="IDTurma" required>
                                <option value="">Selecione</option>
                                @foreach($Turmas as $t)
                                <option value="{{$t->IDTurma}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <label>Cor</label>
                            <select name="Cor" class="form-control">
                                <option value="Não Informado" {{isset($Registro->Cor) && $Registro->Cor == "Não Informado" ? 'selected' : ''}}>Não Informado</option>
                                <option value="Branca" {{isset($Registro->Cor) && $Registro->Cor == "Branca" ? 'selected' : ''}}>Branca</option>
                                <option value="Preta" {{isset($Registro->Cor) && $Registro->Cor == "Preta" ? 'selected' : ''}}>Preta</option>
                                <option value="Parda" {{isset($Registro->Cor) && $Registro->Cor == "Parda" ? 'selected' : ''}}>Parda</option>
                                <option value="Amarela" {{isset($Registro->Cor) && $Registro->Cor == "Amarela" ? 'selected' : ''}}>Amarela</option>
                                <option value="Indigena" {{isset($Registro->Cor) && $Registro->Cor == "Indigena" ? 'selected' : ''}}>Indígena</option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <label>Sexo<strong class="text-danger">*</strong></label>
                            <select name="Sexo" class="form-control" required>
                                <option value="M" {{isset($Registro->Sexo) && $Registro->Sexo == "M" ? 'selected' : ''}}>Masculino</option>
                                <option value="F" {{isset($Registro->Sexo) && $Registro->Sexo == "F" ? 'selected' : ''}}>Feminino</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Necessidades Especiais</label>
                            <select class="form-control" name="NEE">
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->NEE) && $Registro->NEE == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->NEE) && $Registro->NEE == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Acompanhamento Médico</label>
                            <select class="form-control" name="AMedico">
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->AMedico) && $Registro->AMedico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->AMedico) && $Registro->AMedico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Acompanhamento Psicológico</label>
                            <select class="form-control" name="APsicologico">
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->APsicologico) && $Registro->APsicologico == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->APsicologico) && $Registro->APsicologico == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Tem Alergia?</label>
                            <select class="form-control" name="Alergia">
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->Alergia) && $Registro->Alergia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Alergia) && $Registro->Alergia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Utiliza Transporte Escolar?</label>
                            <select class="form-control" name="Transporte">
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->Transporte) && $Registro->Transporte == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Transporte) && $Registro->Transporte == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Recebe Bolsa Família?</label>
                            <select class="form-control" name="BolsaFamilia">
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->BolsaFamilia) && $Registro->BolsaFamilia == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Quilombola</label>
                            <select class="form-control" name="Quilombola">
                                <option value="0" {{isset($Registro->Quilombola) && $Registro->Quilombola == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->Quilombola) && $Registro->Quilombola == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Quilombola) && $Registro->Quilombola == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Ensino Religioso</label>
                            <select class="form-control" name="EReligioso">
                                <option value="0" {{isset($Registro->EReligioso) && $Registro->EReligioso == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->EReligioso) && $Registro->EReligioso == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->EReligioso) && $Registro->EReligioso == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Educação Física</label>
                            <select class="form-control" name="EFisica">
                                <option value="0" {{isset($Registro->EFisica) && $Registro->EFisica == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->EFisica) && $Registro->EFisica == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->EFisica) && $Registro->EFisica == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Permite Utilização da Imagem</label>
                            <select class="form-control" name="DireitoImagem">
                                <option value="0" {{isset($Registro->DireitoImagem) && $Registro->DireitoImagem == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->DireitoImagem) && $Registro->DireitoImagem == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->DireitoImagem) && $Registro->DireitoImagem == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Observações</label>
                            <input type="text" name="Observacoes" class="form-control" value="{{isset($Registro) ? $Registro->Observacoes : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Rota</label>
                            <select name="IDRota" class="form-control">
                                @foreach($Rotas as $r)
                                <option value="{{$r->IDRota}}" class="rowRota" {{isset($Registro) && $Registro->IDRota == $r->IDRota ? 'selected' : ''}}>{{$r->Descricao}} - {{$r->Motorista}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Data de Entrada</label>
                            <input type="date" name="DTEntrada" class="form-control" value="{{isset($Registro) ? $Registro->DTEntrada : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Tipo Sanguíneo</label>
                            <input type="text" name="TPSangue" class="form-control" value="{{isset($Registro) ? $Registro->TPSangue : ''}}">
                        </div>
                        <div class="col-sm-6">
                            <label>Em caso de febre a escola está autorizada a tomar medicação antitérmica?</label>
                            <select class="form-control" name="Medicacao">
                                <option value="0" {{isset($Registro->Medicacao) && $Registro->Medicacao == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->Medicacao) && $Registro->Medicacao == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Medicacao) && $Registro->Medicacao == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Autorização para tempo integral</label>
                            <select class="form-control" name="Integral">
                                <option value="0" {{isset($Registro->Integral) && $Registro->Integral == '0' ? 'selected' : ''}}>Não Informado</option>
                                <option value="1" {{isset($Registro->Integral) && $Registro->Integral == '1' ? 'selected' : ''}}>Sim</option>
                                <option value="0" {{isset($Registro->Integral) && $Registro->Integral == '0' ? 'selected' : ''}}>Não</option>
                            </select>
                        </div>
                        <div class="checkboxEscolas">
                            <div class="form-check escola">
                                <br>
                                <input class="form-check-input" type="checkbox" value="1" name="credenciaisLogin" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                 Enviar Credenciais de Acesso ao Portal do Aluno com o E-mail do Aluno Informado na Matrícula
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        @if(in_array(Auth::user()->tipo,[4,4.5,2,2.5]))
                        <div class="col-auto">
                            <button class="btn btn-fr">Salvar</button>
                        </div>
                        @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
                        <div class="col-auto">
                            <button class="btn btn-warning text-white btn-renovar" type="button">Renovar</button>
                        </div>
                        @endif
                        <div class="col-auto">
                            <a class="btn btn-light" href="{{route('Alunos/index')}}">Cancelar</a>
                        </div>
                        @endif
                        @if(isset($Registro) && in_array(Auth::user()->tipo,[4,2,4.5,2.5])) 
                        <div class="col-auto">
                            <a href="{{route('Alunos/Comprovante/Matricula',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Matrícula e Frequência</a>
                            <a href="{{route('Alunos/Comprovante/Frequencia',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Frequência</a>
                            <a href="{{route('Alunos/Comprovante/Concluinte',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Provavel Concluinte</a>
                            <a href="{{route('Alunos/Comprovante/Conclusao',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Conclusão</a>
                            <a href="{{route('Alunos/Comprovante/Escolaridade',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Declaração de Escolaridade</a>
                            <a href="{{route('Alunos/Comprovante/Vaga',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Atestado de Vaga</a>
                            <a href="{{route('Alunos/Comprovante/Prematricula',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Ficha de Matrícula</a>
                            <a href="{{route('Alunos/Comprovante/Etnico',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Autodeclaração Étnico-Racial</a>
                            <a href="{{route('Alunos/Comprovante/Responsabilidade',$Registro->IDAluno)}}" class="btn btn-fr btn-xs">Termo de Ciência e Responsabilidade</a>
                        </div>
                        @endif
                    </form>
                    </div>
                    @if(isset($Registro->IDAluno) && isset($Registro->IDMatricula))
                    <hr>
                    <h5>Remanejamentos e Reclassificações do Aluno</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <form action="{{route('Alunos/Reclassificar')}}" method="POST">
                                @csrf
                                <input type="hidden" name="IDAluno" value="{{$Registro->IDAluno}}">
                                <label>Série de Destino</label>
                                <select class="form-control" name="IDTurma">
                                    <option value="">Selecione</option>
                                    @foreach($Turmas as $t)
                                    <option value="{{$t->IDTurma}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                                    @endforeach
                                </select>
                                <br>
                                <table class="table">
                                    <thead class="bg-fr text-white">
                                        <tr align="center">
                                            <th colspan="2">Reclassificações</th>
                                        </tr>
                                        <tr align="center">
                                            <th scope="col">Origem</th>
                                            <th scope="col">Destino</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($Reclassificacoes as $rc)
                                    <tr>
                                        <td>{{$rc->Origem}}</td>
                                        <td>{{$rc->Destino}}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-fr">Reclassificar</button>
                            </form>
                        </div>
                        <div class="col-sm-6">
                            <form action="{{route('Alunos/Remanejar')}}" method="POST">
                                @csrf
                                <input type="hidden" name="IDAluno" value="{{$Registro->IDAluno}}">
                                <label>Turma de Destino</label>
                                <select class="form-control" name="IDTurma">
                                    <option value="">Selecione</option>
                                    @foreach($Turmas as $t)
                                    <option value="{{$t->IDTurma}}" {{isset($Registro->IDTurma) && $Registro->IDTurma == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                                    @endforeach
                                </select>
                                <br>
                                <table class="table">
                                    <thead class="bg-fr text-white">
                                        <tr align="center">
                                            <th colspan="2">Remanejamentos - {{date('Y')}}</th>
                                        </tr>
                                        <tr align="center">
                                            <th scope="col">Origem</th>
                                            <th scope="col">Destino</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($Remanejamentos as $rm)
                                        <tr>
                                            <td>{{$rc->Origem}} - {{$rm->OrigemNome}}</td>
                                            <td>{{$rc->Destino}} - {{$rm->DestinoNome}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button class="btn btn-fr">Remanejar</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            @if(isset($Registro) && $Vencimento->lt($Hoje) && $Registro->ANO <= date('Y'))
            <form class="form-controls" id="formRenova" method="POST" action="{{route('Alunos/Renovar')}}" style="display:hidden">
                @csrf
                <input type="hidden" name="IDAluno" value="{{$Registro->IDAluno}}">  
            </form>
            @endif
        </div>
    </div>
    <script>
        @if(isset($Registro) && $Registro->IDRota > 0)
            $(".rowRota").show()
        @else
            $(".rowRota").hide()
            $("select[name=IDRota]").val("")
        @endif

        

        $("select[name=Transporte]").on("change",function(){
            if($(this).val() > 0){
                $(".rowRota").show()
            }else{
                $(".rowRota").hide()
            }
        })

        $('input[name=CEP]').on("change",function(e){
            if( $(this).val().length == 9){
                var cep = $(this).val();
                var url = "https://viacep.com.br/ws/"+cep+"/json/";
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(dados){
                        $("input[name=UF]").val(dados.uf).change();
                        $("input[name=Cidade]").val(dados.localidade);
                        $("input[name=Bairro]").val(dados.bairro);
                        $("input[name=Rua]").val(dados.logradouro);
                    }
                })
            }            
        })
        //
        $("input[name=CEP]").inputmask('99999-999')
        $("input[name=Celular]").inputmask('(99) 9 9999-9999')
        $("input[name=CLResponsavel]").inputmask('(99) 9 9999-9999')
        $("input[name=CPF]").inputmask('999.999.999-99')
        $("input[name=CPFAluno]").inputmask('999.999.999-99')
        $("input[name=CPFResponsavel]").inputmask('999.999.999-99')
        $("input[name=RGResponsavel]").inputmask('99-999-999')
        $("input[name=RGPais]").inputmask('99.999.999')
        $("input[name=RG]").inputmask('99.999.999')
        //
        function displaySelectedImage(event, elementId) {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        //
        $('.btn-renovar').on("click",function(){
            if(confirm("Deseja Renovar a Matrícula do Aluno?")){
                $("#formRenova").submit()
            }
        })
        //
    </script>
</x-educacional-layout>