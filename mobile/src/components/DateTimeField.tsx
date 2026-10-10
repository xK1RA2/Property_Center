import DateTimePicker, { DateTimePickerAndroid } from '@react-native-community/datetimepicker';
import { Platform, Pressable, Text, View } from 'react-native';

import { colors } from './ui';

/**
 * Date + time picker.
 * Android: native date dialog then time dialog. iOS: inline compact picker.
 * Web has no native picker, so it falls back to a text field (YYYY-MM-DDTHH:mm).
 */
export function DateTimeField({
  label,
  value,
  onChange,
  minimumDate = new Date(),
}: {
  label: string;
  value: Date;
  onChange: (date: Date) => void;
  minimumDate?: Date;
}) {
  const openAndroid = () => {
    DateTimePickerAndroid.open({
      value,
      mode: 'date',
      minimumDate,
      onChange: (e, date) => {
        if (e.type !== 'set' || !date) return;
        DateTimePickerAndroid.open({
          value: date,
          mode: 'time',
          is24Hour: true,
          onChange: (te, time) => {
            if (te.type !== 'set' || !time) return;
            const next = new Date(date);
            next.setHours(time.getHours(), time.getMinutes(), 0, 0);
            onChange(next);
          },
        });
      },
    });
  };

  return (
    <View style={{ marginBottom: 14 }}>
      <Text style={{ fontSize: 13, fontWeight: '600', color: colors.muted, marginBottom: 6 }}>{label}</Text>

      {Platform.OS === 'ios' ? (
        <View style={{ alignItems: 'flex-start' }}>
          <DateTimePicker
            value={value}
            mode="datetime"
            minimumDate={minimumDate}
            onChange={(_, date) => date && onChange(date)}
            accentColor={colors.primary}
          />
        </View>
      ) : Platform.OS === 'android' ? (
        <Pressable
          onPress={openAndroid}
          style={{
            backgroundColor: colors.card,
            borderWidth: 1,
            borderColor: colors.border,
            borderRadius: 10,
            paddingHorizontal: 12,
            paddingVertical: 12,
          }}>
          <Text style={{ fontSize: 15, color: colors.text }}>{formatDateTime(value)}</Text>
        </Pressable>
      ) : (
        <Text style={{ color: colors.muted }}>{formatDateTime(value)} (use the mobile app to change)</Text>
      )}
    </View>
  );
}

export const formatDateTime = (d: Date) =>
  d.toLocaleString(undefined, { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
