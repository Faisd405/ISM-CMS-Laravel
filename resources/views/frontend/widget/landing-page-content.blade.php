<div class="box-wrap home-intro">
    <div class="container">
        <div class="home-intro-flex">
            <div class="swiper-container intro-content">
                <div class="swiper-wrapper">
                    @foreach ($widget['module']['posts'] as $post)
                    <div class="swiper-slide">
                        <div class="item-intro-content">
                            <div class="title-heading">
                                <h1>
                                    {{ $post->fieldLang('title') }}
                                </h1>
                            </div>
                            <article class="summary-content">
                                {!! $post->fieldLang('intro') !!}
                            </article>
                            <div class="box-btn">
                                <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="swiper-container intro-img">
                <div class="swiper-wrapper">
                    @foreach ($widget['module']['posts'] as $post)
                    <div class="swiper-slide">
                        <div class="thumbnail-img">
                            <img src="{{ $post['cover_src'] }}" alt="">
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <!-- <div class="popup-content">
                <div class="modal fade dark modal-form" id="modal_1" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Steel for Indonesia Prosperity</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <article>
                                    <p>At present, Indonesia’s steel industry employs about 300.000 persons in Indonesia; partly in the steel companies themselves and partly in different supplier companies.</p>
                                    <p>The steel industry generates substantial export earning and tax revenues. The Consumption contribute directly to GNP and the tax revenues help, in different ways, to fund investments in public services.</p>
                                    <p>The Picture beside gives an example of the steel industry’s contribution to social development, the environmental benefits of steel and the far-reaching use of steel in many forms in our daily lives</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade dark modal-form" id="modal_2" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Steel bridges our nations</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <article>
                                    <p>Sebagai salah satu industri strategis, industri baja berperan penting dalam menunjang kebutuhan sarana transportasi (darat, laut maupun udara) bagi penduduk di suatu negara.  </p>
                                    <p>Beberapa jenis baja dibutuhkan sebagai bahan baku untuk produksi beberapa alat transportasi publik antara lain : kereta api, kapal laut, dan pesawat terbang. Ditengah era kemajuan transportasi modern, industri baja memiliki peluang sekaligus tantangan dalam berinovasi serta memenuhi kualitas produk.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade dark modal-form" id="modal_3" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Steel is our mother Industry</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <article>
                                    <p>Industri besi dan baja merupakan induk dari semua industri dasar karena sifat lentur serta kekuatannya.  Semua industri baik berat, sedang atau ringan bergantung padanya untuk permesinan dan konstruksi. Dengan demikian, menyediakan basis untuk industri lain.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade dark modal-form" id="modal_4" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Steel in our everyday life</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <article>
                                    <p>Can we imagine life without steel ? Talk about Steel is not strength only but steel is part an art and country or city image . High and art design of towers building , art design of bridges until appearances of our  transportation modes and our homes ,all are used steel. Steel is become part of our life to support man life quality and prosperity.</p>
                                    <p>Steel - its around us , its absolutely everywhere . It is in our kitchen , homes, cars , schools, malls ,office buildings ,train ,bridges, even in our park garden. Steel is being used to due to strength, formability, light steel and durability to things that we can develop products design and fabricate or manufacture it ,so we can use in our daily life. It is used as major component in infrastructure, ship buildings , automotive ,oil gas industries,  electricity and water distribution , machinery industries, tools ,home appliances and much more.</p>
                                    <p>Steel with  its material characteristics is currently one of the most common materials used around the world. Each year over 1,8 billion tons of steel is produced and is identified by various steel grades and standards. In Indonesia steel consumption growth ~ 6 % every year and steel consumption in 2019 is 15,9 million ton.</p>
                                    <p>How steel is used Today . Steel is used in many applications and purposes in modern and heavy construction such as high tower and  building , sport stadiums, airports , bridges , fantacy  world turism objects. Steel is also used in many different type of vehicles and appliance , mining industries , oil gas pipe transportation ,heavy equipment.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade dark modal-form" id="modal_3" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Steel is Our Future</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <article>
                                    <p>Sejak ditemukan penggunaannya pada tahun 1500 SM, Baja berperan sangat penting dalam kehidupan kita sampai hari ini dan Baja akan terus berkembang dengan berbagai fungsi dan bentuk menjadi unsur penting dalam membentuk kehidupan manusia modern di masa yang akan datang.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    <div class="thumbnail-img">
        <img src="{{ $widget['module']['section']['cover_src'] }}" alt="">
    </div>
</div>
