import { z } from "zod";

export const changeRWSchema = z.object({
    id: z.coerce.number().min(1),
});

export type ChangeRWSchema = z.infer<typeof changeRWSchema>;
