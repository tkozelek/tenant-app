@foreach($bundles as $bundle)
    <x-bundle-accordion :bundle="$bundle" />
@endforeach
