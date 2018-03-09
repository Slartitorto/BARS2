# usage: sh ./drill.sh filename.TXT
# This script creates three gcode files suited for iModula CNC from source files created by eagle CAD
# filename.dri and filename.TXT are output from Seeed_Gerber_Generater_2-layer.cam "DrillHoles" sections
# (do only "process section")
# ensure filename.dri exists and it has correct T01 and T02 values
# ensure T01 contains only angle, angle are 4 and T02 contains only vias
# ensure other holes are from T03
# ensure T_angle is equal to diameter of angle holes you have chose in eagle and T_vias is equal to diameter of via holes
# Then do simply "cat filename.angle.gcode > /dev/usb/lp0" to the raspberry which iModela is connected through usb 

T_angle="0.0197inch"
T_vias="0.0217inch"

X_OFFSET="2000"
Y_OFFSET="2000"
Z_DRILL_SPEED="17000"  #speed of drilling Z movement, be carefull and use low speed (i.e. 12000) for very tiny bits (0.5 mm)

Z_ANGLE="-7000"   #holes deep on support to insert barrel
Z_PCB="-4200"     #holes deep on PCB
Z_FREE="-2000"    #deep wich ensures free x-y movement over PCB and barrell

file=`basename $1 .TXT`
if [ -f $file.TXT ] && [ -f $file.dri ]
then
  T01=`grep $T_angle $file.dri| awk '{print $1}'`
  T02=`grep $T_vias $file.dri| awk '{print $1}'`
  angle_qty=`awk /T01$/,/T02$/'{print $0}' $1 | wc -l|awk '{print $1}'`
  if [ "$T01" = "T01" ] && [ "$T02" = "T02" ] && [ "$angle_qty" = "6" ]  # 4 holes between T01 and T02 plus T01 and T02 strings
  then
    awk /T01$/,/T02$/'{print $0}' $1 > $file.angle.temp
    awk /T02$/,/T03$/'{print $0}' $1 > $file.vias.temp
    awk /T03$/,/EOF/'{print $0}' $1 > $file.pcb.temp
    # prepare vias and pcb gcode
    for i in vias pcb
    do
      echo "%\r\nG90\r\nG20\r\nM03\r\nG01 Z$Z_FREE\r" > $file.$i.gcode
      grep ^X $file.$i.temp | grep Y | sed 's/Y/ /' | sed 's/X/ /'|sed 's/$/ '$X_OFFSET' '$Y_OFFSET' '$Z_PCB' '$Z_DRILL_SPEED' '$Z_FREE'/' | sort -n -k 1,2 | awk '{print "G00 X"(int($1/10)+$3)" Y"(int($2/10+$4))"\r\nG01 Z"$5" F"$6"\r\nG00 Z"$7"\r"}' >> $file.$i.gcode
      echo "M05\r\nG00 X0 Y0 Z0\r\nM02\r\n%" >> $file.$i.gcode
    done
    # prepare angle gcode for holes guideline
    echo "%\r\nG90\r\nG20\r\nM03\r\nG01 Z-2000\r" > $file.angle.gcode
    grep ^X $file.angle.temp | grep Y | sed 's/Y/ /' | sed 's/X/ /'|sed 's/$/ '$X_OFFSET' '$Y_OFFSET' '$Z_ANGLE' '$Z_DRILL_SPEED' '$Z_FREE'/' | sort -n -k 1,2 | awk '{print "G00 X"(int($1/10)+$3)" Y"(int($2/10+$4))"\r\nG01 Z"$5" F"$6"\r\nG00 Z"$7"\r"}' >> $file.angle.gcode
    echo "M05\r\nG00 X0 Y0 Z0\r\nM02\r\n%" >> $file.angle.gcode
    rm $file.vias.temp
    rm $file.angle.temp
    rm $file.pcb.temp
    echo "Done !"
  else
    echo "T01 doesn't match with $T_angle and/or T02 doesn't match with $T_vias in $file.dri or angles are not 4"
  fi
else
  echo "$file.TXT and/or $file.dri doesn't exists"
fi
