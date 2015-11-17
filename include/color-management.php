<div class="magick-header">

<p class="lead magick-description">Color management has changed significantly between ImageMagick version 6.7.5-5 and 6.8.0-3 in order to better conform to color and grayscale standards.</p>

<p>The first major change was to swap -colorspace RGB and -colorspace sRGB. In earlier versions, RGB really meant non-linear sRGB. With the completion of the changes, RGB now means linear color and sRGB means non-linear color in terms of their respective colorspaces.</p>

<p>ImageMagick supports color profiles, however, for images without a profile or a declaration of colorspace, ImageMagick assumes non-linear sRGB.  Most image processing algorithms assume a linear colorspace, therefore it might be prudent to convert to linear color or remove the gamma function before certain image processing algorithms are applied. For example,</p>

<pre>
convert myimage.jpg -colorspace RGB -resize 200% -colorspace sRGB mybigimage.jpg
</pre>

<p>To declare that an image is linear RGB rather than sRGB, you can use the set option:</p>

<pre>
convert myimage.png -set colorspace RGB myRGBimage.png
</pre>

<p>Afterwards, the verbose information for the output file lists the colorspace as RGB. This only works on image types containing meta data that distinguishes between linear RGB and non-linear sRGB, such as PNG and GIF. Therefore, if the above command is run with a JPG or TIF output format, the verbose information for the colorspace still shows sRGB. In order to properly have the JPG output know that it is linear RGB, include an appropriate color profile.</p>


<p>The second major change treats any grayscale image as linear rather than non-linear, as it was previously.  This change is appropriate, since many types of processing requires a linear colorspace. This means that the conversion of a color image to grayscale via <code>-colorspace gray</code> looks darker relative to previous versions of ImageMagick (note that desaturating to convert to grayscale does not convert the image to linear grayscale). If you prefer to keep the conversion to non-linear grayscale, set the colorspace of the input to linear RGB so that <code>-colorspace gray</code> does not apply the gamma correction during the conversion process. For example, the following produces a non-linear grayscale result.</p>

<pre>
convert myimage.png -set colorspace RGB -colorspace gray myRGBimage.png
</pre>

<p>The same concept is needed when separating channels. Normally, the conversion to separate each channel of an sRGB color image produces separate linear grayscale images. However the same concept can be applied, if it is desired to keep the separate channels as non-linear grayscale. For example, the following produces non-linear grayscale channels.</p>

<pre>
convert myimage.png -set colorspace RGB -separate myimage_channels_%d.png
</pre>

<p>When separating and recombining channels, with potential intermediate processing, it is important to identify the colorspace used, especially during the recombination. For example,</p>

<pre>
convert myimage.png -separate myimage_channels_%d.png
convert myimage_channels_*.png -combine myimage2.png
</pre>

<p>In the above example, the result is darker than the original, because the channels were separate as linear gray and subsequently combined as linear color. In order to return the channels back to sRGB, one must change the colorspace from RGB back to sRGB after the <code>-combine</code> step.</p>

<pre>
convert myimage.png -separate myimage_channels_%d.png
convert myimage_channels_*.png -combine -colorspace sRGB myimage2.png
</pre>

<p>If one desires to separate to non-linear grayscale channels, recombine them later, perhaps after some processing, then use the same concept as above for maintaining non-linear grayscale:</p>

<pre>
convert myimage.png -set colorspace RGB -separate myimage_channels_%d.png
convert myimage_channels_*.png -combine -colorspace RGB -set colorspace sRGB myimage2.png
</pre>

<p>When converting to another colorspace and back, such as between sRGB and HSL, the following two commands handle the first case of linear channels and the second case of non-linear channels:</p>

<pre>
convert myimage.png -colorspace HSL -separate myimage_channels_%d.png
convert myimage_channels_*.png -set colorspace HSL -combine -colorspace sRGB myimage2.png
</pre>

<pre>
convert myimage.png -set colorspace RGB -colorspace HSL -separate myimage_channels_%d.png
convert myimage_channels_*.png -set colorspace HSL -combine -colorspace RGB -set colorspace sRGB myimage2.png
</pre>

<p>A majority of the image formats assume an sRGB colorspace (e.g. JPEG, PNG, etc.). A few support only linear RGB (e.g. EXR, DPX, CIN, HDR) or only linear GRAY (e.g. PGM). A few formats support CMYK. For example JPG does, but PNG does not. Then there is the occasional format that also supports LAB (that is CieLAB) (e.g. TIFF, PSD, JPG, JP2). For additional information, see the <a href="http://www.imagemagick.org/script/formats.php#colorspace" >Colorspace</a> and <a href="http://www.imagemagick.org/script/formats.php#supported" >Supported Formats</a> pages.</p>

<p>When specifying individual colors as <code>rgb(...)</code> or hex, these colors will still be interpreted as non-linear, that is, as sRGB colors. However if one wants to create linear colors, use <code>icc-color(rgb,r,g,b)"</code>, where <code>r</code>, <code>g</code>, and <code>b</code> are in the range 0 to 1. See the <a href="http://www.imagemagick.org/script/color.php" >Color</a> page.</p>

<p>There are now also distinctions between linear and non-linear shades of gray. Any named shade of gray, such as black, white or numbered grays like <code>gray50</code> are non-linear gray. However, <code>gray(...)</code> is now linear gray. Hex grayscale values are non-linear.</p>

<p>This means that you need to be careful when you create grayscale gradients. For example, <code>gradient:</code>, <code>gradient:"white-black"</code>, <code>gradient:"gray100-gray0"</code> generates non-linear gradients, however <code>gradient:"gray(255)-gray(0)"</code> or <code>gradient:"gray(100%)-gray(0%)"</code> generates linear gradients.</p>

</div>