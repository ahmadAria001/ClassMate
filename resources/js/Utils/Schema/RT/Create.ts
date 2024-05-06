import { z } from 'zod'

export const createSchema = z.object({
    number: z.coerce.number().min(1),
    leader_id: z.coerce.number().min(1)
})

export type CreateSchema = z.infer<typeof createSchema>;
