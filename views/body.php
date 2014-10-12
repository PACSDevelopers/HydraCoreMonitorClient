<?hh

class BodyView extends \HC\View {
    public function init($settings = [], $body = '') {
      $body = <x:frag>
                {$body}
              </x:frag>;
      return $body;
    }
}
