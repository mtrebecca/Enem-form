<?php

namespace Database\Seeders;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class EnemDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                [
                    'name' => 'Maria Treb',
                    'email' => 'maria@enem.dev',
                    'password' => Hash::make('123456'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Prova::query()->exists()) {
            return;
        }

        $banco = Prova::query()->create([
            'titulo' => 'Banco nacional — treino aleatorio',
            'status' => 'ativo',
            'tipo' => 'banco',
        ]);

        $this->seedBancoNacional($banco->id);

        $p1 = Prova::query()->create([
            'titulo' => 'Simulado ENEM — Linguagens e Ciencias Humanas',
            'status' => 'ativo',
            'tipo' => 'simulado',
        ]);

        $this->multiplaEscolha(
            $p1->id,
            'Linguagens',
            'Leia o trecho: "O tempo e um professor severo: cobra cada licao com juros." A expressao em aspas estabelece, predominantemente, uma identificacao entre dois planos semanticos sem conectivo explicito, o que caracteriza a figura de:',
            [
                'A) Metonimia.',
                'B) Metáfora.',
                'C) Hipérbole.',
                'D) Ironia.',
                'E) Eufemismo.',
            ],
            1,
            'INEP/ENEM — estilo Linguagens (interpretacao de figuras de linguagem); gabarito alinhado a classificacao de metáfora. Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p1->id,
            'Ciencias Humanas',
            'No Imperio do Brasil, a extincao legal da escravidao foi sancionada pela Lei Áurea (13 de maio de 1888). A sancao coube, na condicao de regente, a:',
            [
                'A) Dom Pedro II, por decreto pessoal.',
                'B) Princesa Isabel, apos aprovacao parlamentar.',
                'C) Assembleia de 1823.',
                'D) Presidente Prudente de Morais.',
                'E) Conselho de Estado de D. Joao VI.',
            ],
            1,
            'INEP/ENEM — Historia do Brasil (Lei nº 3.353/1888); enunciado no formato oficial de multipla escolha. Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p1->id,
            'Linguagens',
            'Em "Choveu convidados na festa", o emprego figurado de "chover" sugere quantidade exagerada de pessoas. Essa intensificacao corresponde, sobretudo, a:',
            [
                'A) Catacrese.',
                'B) Polissemia.',
                'C) Hipérbole.',
                'D) Antitese.',
                'E) Gradação.',
            ],
            2,
            'INEP/ENEM — Linguagens (figuras de linguagem / exagero intencional). Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p1->id,
            'Ciencias Humanas',
            'A expansao das cidades com ocupacao precaria em periferias e anel viario, em muitos casos, expressa processos de:',
            [
                'A) Reducao da taxa de urbanizacao.',
                'B) Metropolizacao e segregacao socioespacial.',
                'C) Equilibrio da renda intraurbana.',
                'D) Estagnacao absoluta da populacao metropolitana.',
                'E) Extincao das aglomeracoes urbanas.',
            ],
            1,
            'INEP/ENEM — Geografia urbana (periferia e segregacao); formato multipla escolha. Texto adaptado.'
        );

        $p2 = Prova::query()->create([
            'titulo' => 'Simulado ENEM — Ciencias da Natureza e Matematica',
            'status' => 'ativo',
            'tipo' => 'simulado',
        ]);

        $this->multiplaEscolha(
            $p2->id,
            'Ciencias da Natureza',
            'Na fotossintese oxygenica, em presenca de luz, plantas utilizam agua e gas carbonico e liberam, entre outros produtos, qual gas para a atmosfera?',
            [
                'A) N2.',
                'B) CO2.',
                'C) O2.',
                'D) CH4.',
                'E) H2.',
            ],
            2,
            'INEP/ENEM — Biologia (fotossintese); item objetivo com cinco alternativas. Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p2->id,
            'Matematica',
            'Para f(x) = x² - 4x + 3, com x real, o valor minimo de f ocorre quando x e igual a:',
            [
                'A) 0.',
                'B) 1.',
                'C) 2.',
                'D) 3.',
                'E) 4.',
            ],
            2,
            'INEP/ENEM — Matematica (funcao quadratica; vertice em x = -b/2a = 2). Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p2->id,
            'Ciencias da Natureza',
            'Um movel em MRUV tem v0 = 10 m/s e a = 2 m/s² na mesma direcao. Apos t = 5 s, sua velocidade escalar e, em m/s:',
            [
                'A) 10.',
                'B) 15.',
                'C) 20.',
                'D) 25.',
                'E) 30.',
            ],
            2,
            'INEP/ENEM — Fisica (MRUV: v = v0 + at). Texto adaptado.'
        );

        $this->multiplaEscolha(
            $p2->id,
            'Matematica',
            'Uma moeda honesta e lancada duas vezes, de modo independente. A probabilidade de sair cara nas duas vezes e:',
            [
                'A) 1/2.',
                'B) 1/3.',
                'C) 1/4.',
                'D) 1/6.',
                'E) 2/3.',
            ],
            2,
            'INEP/ENEM — Matematica (probabilidade de eventos independentes). Texto adaptado.'
        );
    }

    private function seedBancoNacional(int $provaId): void
    {
        $itens = [
            [
                'Matematica',
                'O MDC entre 12 e 18 e:',
                ['A) 2.', 'B) 3.', 'C) 6.', 'D) 36.', 'E) 72.'],
                2,
                'INEP/ENEM — tipo Matematica basica (MDC); banco proprio, cinco alternativas.',
            ],
            [
                'Matematica',
                'O valor de 2 elevado a 10 e:',
                ['A) 512.', 'B) 1024.', 'C) 2048.', 'D) 256.', 'E) 128.'],
                1,
                'INEP/ENEM — tipo Matematica (potenciacao). Banco proprio.',
            ],
            [
                'Matematica',
                'A raiz quadrada positiva de 144 e:',
                ['A) 10.', 'B) 11.', 'C) 12.', 'D) 14.', 'E) 16.'],
                2,
                'INEP/ENEM — tipo Matematica (radiciação). Banco proprio.',
            ],
            [
                'Matematica',
                'Em R, se 3x - 7 = 8, entao x vale:',
                ['A) 3.', 'B) 4.', 'C) 5.', 'D) 6.', 'E) 7.'],
                2,
                'INEP/ENEM — tipo Matematica (equacao linear). Banco proprio.',
            ],
            [
                'Matematica',
                'A area de um quadrado de lado 7 cm, em cm², e:',
                ['A) 14.', 'B) 28.', 'C) 49.', 'D) 64.', 'E) 21.'],
                2,
                'INEP/ENEM — tipo Matematica (geometria plana). Banco proprio.',
            ],
            [
                'Matematica',
                'Num dado honesto de seis faces, a probabilidade de sair face 2 num lancamento e:',
                ['A) 1/2.', 'B) 1/3.', 'C) 1/6.', 'D) 1/12.', 'E) 1/36.'],
                2,
                'INEP/ENEM — tipo Matematica (probabilidade). Banco proprio.',
            ],
            [
                'Fisica',
                'No SI, a unidade de forca e o:',
                ['A) J.', 'B) W.', 'C) N.', 'D) Pa.', 'E) kg.'],
                2,
                'INEP/ENEM — tipo Fisica (unidades). Banco proprio.',
            ],
            [
                'Fisica',
                'A ordem de grandeza da velocidade da luz no vacuo e cerca de:',
                ['A) 3 km/s.', 'B) 300 km/s.', 'C) 300 mil km/s.', 'D) 3 milhoes km/s.', 'E) 30 km/s.'],
                2,
                'INEP/ENEM — tipo Fisica (c ~ 3 x 10^8 m/s). Banco proprio.',
            ],
            [
                'Quimica',
                'O simbolo do ouro e:',
                ['A) Ag.', 'B) Au.', 'C) O.', 'D) Pb.', 'E) Fe.'],
                1,
                'INEP/ENEM — tipo Quimica (tabela periodica). Banco proprio.',
            ],
            [
                'Quimica',
                'A formula da agua comum e:',
                ['A) CO2.', 'B) O2.', 'C) H2O.', 'D) NaCl.', 'E) NH3.'],
                2,
                'INEP/ENEM — tipo Quimica (formulas). Banco proprio.',
            ],
            [
                'Biologia',
                'Em celulas vegetais eucarioticas, a fotossintese ocorre principalmente em:',
                ['A) Mitocondrias.', 'B) Ribossomos.', 'C) Cloroplastos.', 'D) Lisossomos.', 'E) Centrossomo.'],
                2,
                'INEP/ENEM — tipo Biologia (citologia). Banco proprio.',
            ],
            [
                'Biologia',
                'Nos alveolos pulmonares, as trocas gasosas entre sangue e ar envolvem sobretudo:',
                ['A) Apenas N2.', 'B) O2 e CO2.', 'C) Apenas H2O vapor.', 'D) H2 livre.', 'E) Apenas CO.'],
                1,
                'INEP/ENEM — tipo Biologia (fisiologia). Banco proprio.',
            ],
            [
                'Geografia',
                'A capital federal do Brasil desde 1960 e:',
                ['A) Rio de Janeiro.', 'B) Sao Paulo.', 'C) Brasilia.', 'D) Salvador.', 'E) Curitiba.'],
                2,
                'INEP/ENEM — tipo Geografia (espaco politico). Banco proprio.',
            ],
            [
                'Geografia',
                'O maior estado brasileiro em area territorial e:',
                ['A) Para.', 'B) Mato Grosso.', 'C) Amazonas.', 'D) Bahia.', 'E) Minas Gerais.'],
                2,
                'INEP/ENEM — tipo Geografia (IBGE). Banco proprio.',
            ],
            [
                'Historia',
                'A Proclamacao da Republica no Brasil ocorreu em:',
                ['A) 1822.', 'B) 1888.', 'C) 1889.', 'D) 1891.', 'E) 1900.'],
                2,
                'INEP/ENEM — tipo Historia (Republica). Banco proprio.',
            ],
            [
                'Historia',
                'A Lei Áurea, que extinguiu a escravidao, e de:',
                ['A) 1824.', 'B) 1850.', 'C) 1888.', 'D) 1891.', 'E) 1822.'],
                2,
                'INEP/ENEM — tipo Historia (Lei Áurea). Banco proprio.',
            ],
            [
                'Filosofia',
                'Socrates e classicamente associado a:',
                ['A) Atomismo.', 'B) Dialogo e ironia filosofica.', 'C) Epicurismo.', 'D) Estoicismo romano.', 'E) Ceticismo radical.'],
                1,
                'INEP/ENEM — tipo Filosofia (antiga). Banco proprio.',
            ],
            [
                'Literatura',
                'O genero lirico tende a privilegiar:',
                ['A) Narracao epica objetiva.', 'B) Expressao subjetiva de estados de alma.', 'C) Apenas dialogo teatral.', 'D) Cronica jornalistica neutra.', 'E) Manual tecnico.'],
                1,
                'INEP/ENEM — tipo Literatura (generos). Banco proprio.',
            ],
            [
                'Ingles',
                'Complete: She ___ to school every day.',
                ['A) go.', 'B) goes.', 'C) going.', 'D) are going.', 'E) went.'],
                1,
                'INEP/ENEM — tipo Ingles (presente simples). Banco proprio.',
            ],
            [
                'Sociologia',
                'Marx analisa a exploracao do trabalho assalariado na logica do:',
                ['A) Feudalismo puro.', 'B) Capitalismo e mais-valia.', 'C) Socialismo utopico seculo XVI.', 'D) Tribalismo.', 'E) Anarquismo individual.'],
                1,
                'INEP/ENEM — tipo Sociologia (teoria classica). Banco proprio.',
            ],
        ];

        foreach ($itens as [$disciplina, $enunciado, $alternativas, $correta, $fonte]) {
            $this->multiplaEscolha($provaId, $disciplina, $enunciado, $alternativas, $correta, $fonte);
        }
    }

    private function multiplaEscolha(int $provaId, string $disciplina, string $enunciado, array $alternativas, int $indiceCorreto, ?string $fonte = null): void
    {
        if (count($alternativas) !== 5) {
            throw new InvalidArgumentException('Questao no padrao ENEM: exatamente 5 alternativas (A a E).');
        }

        $opcoes = [];
        foreach ($alternativas as $i => $texto) {
            $opcoes[] = [
                'texto' => $texto,
                'correta' => $i === $indiceCorreto,
            ];
        }

        Questao::query()->create([
            'prova_id' => $provaId,
            'disciplina' => $disciplina,
            'enunciado' => $enunciado,
            'opcoes' => $opcoes,
            'fonte' => $fonte,
        ]);
    }
}
