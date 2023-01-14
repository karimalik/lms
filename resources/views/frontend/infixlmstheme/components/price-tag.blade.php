<div>
      <span class="prise_tag">

               @if (@$discount!=null)
              <span class="prev_prise">
                  {{getPriceFormat($price)}}
                  </span>
          @endif

              <span>
            @if (@$discount!=null)
                      {{getPriceFormat($discount)}}
                  @else
                      {{getPriceFormat($price)}}
                  @endif

          </span>
</span>
</div>
