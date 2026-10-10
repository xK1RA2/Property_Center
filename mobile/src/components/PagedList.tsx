import { useInfiniteQuery } from '@tanstack/react-query';
import { ReactElement } from 'react';
import { ActivityIndicator, FlatList, RefreshControl } from 'react-native';

import type { Paginated } from '@/lib/types';

import { colors, Empty, ErrorState, Loading } from './ui';

export function PagedList<T extends { id: number }>({
  queryKey,
  fetchPage,
  renderItem,
  header,
  emptyTitle,
  emptyHint,
}: {
  queryKey: readonly unknown[];
  fetchPage: (page: number) => Promise<Paginated<T>>;
  renderItem: (item: T) => ReactElement;
  header?: ReactElement;
  emptyTitle: string;
  emptyHint?: string;
}) {
  const q = useInfiniteQuery({
    queryKey,
    queryFn: ({ pageParam }) => fetchPage(pageParam),
    initialPageParam: 1,
    getNextPageParam: (last) =>
      last.meta.current_page < last.meta.last_page ? last.meta.current_page + 1 : undefined,
  });

  const items = q.data?.pages.flatMap((p) => p.data) ?? [];

  if (q.isLoading) return <Loading />;
  if (q.isError) return <ErrorState message={(q.error as Error).message} onRetry={() => q.refetch()} />;

  return (
    <FlatList
      data={items}
      keyExtractor={(item) => String(item.id)}
      renderItem={({ item }) => renderItem(item)}
      ListHeaderComponent={header}
      ListEmptyComponent={<Empty title={emptyTitle} hint={emptyHint} />}
      contentContainerStyle={{ padding: 16, flexGrow: 1 }}
      refreshControl={
        <RefreshControl refreshing={q.isRefetching && !q.isFetchingNextPage} onRefresh={() => q.refetch()} tintColor={colors.primary} />
      }
      onEndReachedThreshold={0.4}
      onEndReached={() => q.hasNextPage && !q.isFetchingNextPage && q.fetchNextPage()}
      ListFooterComponent={q.isFetchingNextPage ? <ActivityIndicator style={{ margin: 16 }} color={colors.primary} /> : null}
    />
  );
}
